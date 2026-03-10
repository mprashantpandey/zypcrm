<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AttendanceRiskMonitor extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $tenantFilter = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(13)->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function clearFilters(): void
    {
        $this->dateFrom = now()->subDays(13)->toDateString();
        $this->dateTo = now()->toDateString();
        $this->tenantFilter = '';
    }

    public function render()
    {
        $tenants = Tenant::query()->orderBy('name')->get(['id', 'name']);

        $hasLogs = Schema::hasTable('attendance_action_logs');
        $hasAttendance = Schema::hasTable('student_attendances');

        $riskSummary = [
            'total_logs' => 0,
            'failed_actions' => 0,
            'anomaly_actions' => 0,
            'missed_clockouts' => 0,
        ];
        $topAnomalies = collect();
        $suspiciousOperators = collect();
        $missedClockoutTrend = [
            'labels' => [],
            'series' => [],
        ];

        if ($hasLogs) {
            $baseLogQuery = DB::table('attendance_action_logs')
                ->whereDate('date', '>=', $this->dateFrom)
                ->whereDate('date', '<=', $this->dateTo)
                ->when($this->tenantFilter !== '', fn ($q) => $q->where('tenant_id', (int) $this->tenantFilter));

            $riskSummary['total_logs'] = (clone $baseLogQuery)->count();
            $riskSummary['failed_actions'] = (clone $baseLogQuery)->where('success', false)->count();

            $riskSummary['anomaly_actions'] = (clone $baseLogQuery)
                ->where(function ($q): void {
                    $q->where('action', 'like', 'duplicate_%')
                        ->orWhereRaw("JSON_EXTRACT(meta, '$.anomaly') IS NOT NULL")
                        ->orWhere('message', 'like', '%overlap%')
                        ->orWhere('message', 'like', '%Pattern abuse%');
                })
                ->count();

            $topAnomalies = (clone $baseLogQuery)
                ->selectRaw('action, COUNT(*) as total, SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failed')
                ->where(function ($q): void {
                    $q->where('action', 'like', 'duplicate_%')
                        ->orWhereRaw("JSON_EXTRACT(meta, '$.anomaly') IS NOT NULL")
                        ->orWhere('success', false)
                        ->orWhere('message', 'like', '%overlap%')
                        ->orWhere('message', 'like', '%Pattern abuse%');
                })
                ->groupBy('action')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $suspiciousOperators = (clone $baseLogQuery)
                ->leftJoin('users as operators', 'operators.id', '=', 'attendance_action_logs.operator_id')
                ->selectRaw('attendance_action_logs.operator_id, COALESCE(operators.name, "System") as operator_name, COUNT(*) as total_actions, SUM(CASE WHEN attendance_action_logs.success = 0 THEN 1 ELSE 0 END) as failed_actions, SUM(CASE WHEN attendance_action_logs.action LIKE "duplicate_%" OR JSON_EXTRACT(attendance_action_logs.meta, "$.anomaly") IS NOT NULL THEN 1 ELSE 0 END) as anomaly_actions')
                ->groupBy('attendance_action_logs.operator_id', 'operators.name')
                ->orderByRaw('(SUM(CASE WHEN attendance_action_logs.success = 0 THEN 1 ELSE 0 END) * 2 + SUM(CASE WHEN attendance_action_logs.action LIKE "duplicate_%" OR JSON_EXTRACT(attendance_action_logs.meta, "$.anomaly") IS NOT NULL THEN 1 ELSE 0 END)) DESC')
                ->limit(10)
                ->get();
        }

        if ($hasAttendance) {
            $missedTrendRows = DB::table('student_attendances')
                ->whereDate('date', '>=', $this->dateFrom)
                ->whereDate('date', '<=', $this->dateTo)
                ->where('status', 'present')
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->when($this->tenantFilter !== '', fn ($q) => $q->where('tenant_id', (int) $this->tenantFilter))
                ->selectRaw('date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy(fn ($row) => (string) $row->date);

            $labels = [];
            $series = [];
            $cursor = now()->parse($this->dateFrom);
            $end = now()->parse($this->dateTo);
            while ($cursor->lte($end)) {
                $dateKey = $cursor->toDateString();
                $labels[] = $cursor->format('d M');
                $series[] = (int) ($missedTrendRows[$dateKey]->total ?? 0);
                $cursor->addDay();
            }

            $missedClockoutTrend = [
                'labels' => $labels,
                'series' => $series,
            ];

            $riskSummary['missed_clockouts'] = array_sum($series);
        }

        return view('livewire.admin.attendance-risk-monitor', [
            'tenants' => $tenants,
            'riskSummary' => $riskSummary,
            'topAnomalies' => $topAnomalies,
            'suspiciousOperators' => $suspiciousOperators,
            'missedClockoutTrend' => $missedClockoutTrend,
            'hasLogs' => $hasLogs,
            'hasAttendance' => $hasAttendance,
        ]);
    }
}
