<?php

namespace App\Livewire\Library;

use App\Models\FeePayment;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use App\Models\TenantSubscriptionInvoice;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class ExportCenter extends Component
{
    public string $dataset = 'students';
    public string $status = '';
    public $dateFrom = '';
    public $dateTo = '';
    public string $search = '';

    protected function tenantId(): int
    {
        return (int) auth()->user()->tenant_id;
    }

    public function updatingDataset(): void
    {
        $this->status = '';
    }

    public function exportCsv(): StreamedResponse
    {
        $this->validate([
            'dataset' => ['required', 'in:students,attendance,leaves,payments,invoices'],
            'status' => ['nullable', 'string', 'max:30'],
            'dateFrom' => ['nullable', 'date'],
            'dateTo' => ['nullable', 'date', 'after_or_equal:dateFrom'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        $filename = 'library_export_'.$this->dataset.'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $out = fopen('php://output', 'w');

            match ($this->dataset) {
                'students' => $this->streamStudents($out),
                'attendance' => $this->streamAttendance($out),
                'leaves' => $this->streamLeaves($out),
                'payments' => $this->streamPayments($out),
                'invoices' => $this->streamInvoices($out),
            };

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function render()
    {
        return view('livewire.library.export-center', [
            'statusOptions' => $this->statusOptions(),
            'previewCount' => $this->previewCount(),
        ]);
    }

    private function previewCount(): int
    {
        return match ($this->dataset) {
            'students' => $this->studentsQuery()->count(),
            'attendance' => $this->attendanceQuery()->count(),
            'leaves' => $this->leavesQuery()->count(),
            'payments' => $this->paymentsQuery()->count(),
            'invoices' => $this->invoicesQuery()->count(),
            default => 0,
        };
    }

    private function statusOptions(): array
    {
        return match ($this->dataset) {
            'students' => [
                ['value' => 'active', 'label' => 'Active Membership'],
                ['value' => 'inactive', 'label' => 'Inactive Membership'],
            ],
            'attendance' => [
                ['value' => 'present', 'label' => 'Present'],
                ['value' => 'absent', 'label' => 'Absent'],
                ['value' => 'leave', 'label' => 'Leave'],
            ],
            'leaves' => [
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'approved', 'label' => 'Approved'],
                ['value' => 'rejected', 'label' => 'Rejected'],
            ],
            'payments' => [
                ['value' => 'paid', 'label' => 'Paid'],
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'overdue', 'label' => 'Overdue'],
            ],
            'invoices' => [
                ['value' => 'paid', 'label' => 'Paid'],
                ['value' => 'pending', 'label' => 'Pending'],
                ['value' => 'cancelled', 'label' => 'Cancelled'],
            ],
            default => [],
        };
    }

    private function streamStudents($out): void
    {
        fputcsv($out, ['Student ID', 'Name', 'Email', 'Phone', 'Membership Status', 'Created At']);

        $this->studentsQuery()->orderBy('id')->chunk(500, function ($rows) use ($out): void {
            foreach ($rows as $student) {
                $membership = $student->memberships->firstWhere('tenant_id', $this->tenantId());
                fputcsv($out, [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->phone,
                    $membership->status ?? 'N/A',
                    optional($student->created_at)->toDateTimeString(),
                ]);
            }
        });
    }

    private function streamAttendance($out): void
    {
        fputcsv($out, ['Record ID', 'Student', 'Email', 'Phone', 'Date', 'Status', 'Check In', 'Check Out']);

        $this->attendanceQuery()->orderBy('id')->chunk(1000, function ($rows) use ($out): void {
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    optional($row->user)->name,
                    optional($row->user)->email,
                    optional($row->user)->phone,
                    optional($row->date)->toDateString(),
                    $row->status,
                    $row->check_in,
                    $row->check_out,
                ]);
            }
        });
    }

    private function streamLeaves($out): void
    {
        fputcsv($out, ['Leave ID', 'Student', 'Email', 'Phone', 'Start Date', 'End Date', 'Status', 'Reason']);

        $this->leavesQuery()->orderBy('id')->chunk(1000, function ($rows) use ($out): void {
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    optional($row->user)->name,
                    optional($row->user)->email,
                    optional($row->user)->phone,
                    optional($row->start_date)->toDateString(),
                    optional($row->end_date)->toDateString(),
                    $row->status,
                    $row->reason,
                ]);
            }
        });
    }

    private function streamPayments($out): void
    {
        fputcsv($out, ['Payment ID', 'Student', 'Amount', 'Status', 'Payment Date', 'Method', 'Transaction ID']);

        $this->paymentsQuery()->orderBy('id')->chunk(1000, function ($rows) use ($out): void {
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    optional($row->user)->name,
                    (float) $row->amount,
                    $row->status,
                    $row->payment_date,
                    $row->payment_method,
                    $row->transaction_id,
                ]);
            }
        });
    }

    private function streamInvoices($out): void
    {
        fputcsv($out, ['Invoice ID', 'Invoice No', 'Plan', 'Amount', 'Currency', 'Status', 'Due Date', 'Payment Method', 'Paid At']);

        $this->invoicesQuery()->orderBy('id')->chunk(1000, function ($rows) use ($out): void {
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->invoice_no,
                    optional($row->plan)->name,
                    (float) $row->amount,
                    $row->currency_code,
                    $row->status,
                    optional($row->due_date)->toDateString(),
                    $row->payment_method,
                    optional($row->paid_at)->toDateTimeString(),
                ]);
            }
        });
    }

    private function studentsQuery()
    {
        $query = User::query()
            ->where('role', 'student')
            ->whereHas('memberships', fn ($q) => $q->where('tenant_id', $this->tenantId()))
            ->with(['memberships' => fn ($q) => $q->where('tenant_id', $this->tenantId())]);

        if ($this->status !== '') {
            $query->whereHas('memberships', function ($q): void {
                $q->where('tenant_id', $this->tenantId())->where('status', $this->status);
            });
        }
        $this->applyDateFilter($query, 'created_at');
        $this->applySearchOnUser($query);

        return $query;
    }

    private function attendanceQuery()
    {
        $query = StudentAttendance::query()
            ->where('tenant_id', $this->tenantId())
            ->with('user:id,name,email,phone');

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $this->applyDateFilter($query, 'date');
        $this->applySearchOnRelationUser($query);

        return $query;
    }

    private function leavesQuery()
    {
        $query = StudentLeave::query()
            ->where('tenant_id', $this->tenantId())
            ->with('user:id,name,email,phone');

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $this->applyDateFilter($query, 'start_date');
        $this->applySearchOnRelationUser($query);

        return $query;
    }

    private function paymentsQuery()
    {
        $query = FeePayment::query()
            ->where('tenant_id', $this->tenantId())
            ->with('user:id,name,email,phone');

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $this->applyDateFilter($query, 'payment_date');
        $this->applySearchOnRelationUser($query);

        return $query;
    }

    private function invoicesQuery()
    {
        $query = TenantSubscriptionInvoice::query()
            ->where('tenant_id', $this->tenantId())
            ->with('plan:id,name');

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        $this->applyDateFilter($query, 'due_date');

        return $query;
    }

    private function applyDateFilter($query, string $column): void
    {
        if ($this->dateFrom) {
            $query->whereDate($column, '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate($column, '<=', $this->dateTo);
        }
    }

    private function applySearchOnUser($query): void
    {
        if (trim($this->search) === '') {
            return;
        }
        $search = trim($this->search);
        $query->where(function ($q) use ($search): void {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    private function applySearchOnRelationUser($query): void
    {
        if (trim($this->search) === '') {
            return;
        }
        $search = trim($this->search);
        $query->whereHas('user', function ($q) use ($search): void {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}

