<?php

namespace App\Livewire\Admin;

use App\Models\FeePayment;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use App\Models\Tenant;
use App\Models\TenantSubscriptionInvoice;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app')]
class ExportCenter extends Component
{
    public string $dataset = 'students';
    public $tenantId = '';
    public string $status = '';
    public $dateFrom = '';
    public $dateTo = '';
    public string $search = '';

    public function updatingDataset(): void
    {
        $this->status = '';
    }

    public function exportCsv(): StreamedResponse
    {
        $this->validate([
            'dataset' => ['required', 'in:students,attendance,leaves,payments,invoices'],
            'tenantId' => ['nullable', 'exists:tenants,id'],
            'status' => ['nullable', 'string', 'max:30'],
            'dateFrom' => ['nullable', 'date'],
            'dateTo' => ['nullable', 'date', 'after_or_equal:dateFrom'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        $filename = 'export_'.$this->dataset.'_'.now()->format('Ymd_His').'.csv';

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
        return view('livewire.admin.export-center', [
            'tenants' => Tenant::query()->orderBy('name')->get(['id', 'name']),
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
        fputcsv($out, [
            'Student ID', 'Name', 'Email', 'Phone', 'Primary Tenant', 'Memberships',
            'Membership Statuses', 'Created At',
        ]);

        $this->studentsQuery()
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($out): void {
                foreach ($rows as $student) {
                    $memberships = $student->memberships
                        ->map(fn ($m) => optional($m->tenant)->name)
                        ->filter()
                        ->implode(' | ');

                    $membershipStatuses = $student->memberships
                        ->map(fn ($m) => ($m->tenant?->name ?? 'Tenant').':'.($m->status ?? 'unknown'))
                        ->implode(' | ');

                    fputcsv($out, [
                        $student->id,
                        $student->name,
                        $student->email,
                        $student->phone,
                        optional($student->tenant)->name,
                        $memberships,
                        $membershipStatuses,
                        optional($student->created_at)->toDateTimeString(),
                    ]);
                }
            });
    }

    private function streamAttendance($out): void
    {
        fputcsv($out, [
            'Record ID', 'Tenant', 'Student', 'Email', 'Phone', 'Date',
            'Status', 'Check In', 'Check Out', 'Created At',
        ]);

        $this->attendanceQuery()
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($out): void {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        optional($row->tenant)->name,
                        optional($row->user)->name,
                        optional($row->user)->email,
                        optional($row->user)->phone,
                        optional($row->date)->toDateString(),
                        $row->status,
                        $row->check_in,
                        $row->check_out,
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });
    }

    private function streamLeaves($out): void
    {
        fputcsv($out, [
            'Leave ID', 'Tenant', 'Student', 'Email', 'Phone', 'Start Date',
            'End Date', 'Status', 'Reason', 'Created At',
        ]);

        $this->leavesQuery()
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($out): void {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        optional($row->tenant)->name,
                        optional($row->user)->name,
                        optional($row->user)->email,
                        optional($row->user)->phone,
                        optional($row->start_date)->toDateString(),
                        optional($row->end_date)->toDateString(),
                        $row->status,
                        $row->reason,
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });
    }

    private function streamPayments($out): void
    {
        fputcsv($out, [
            'Payment ID', 'Tenant', 'Student', 'Email', 'Phone', 'Amount',
            'Status', 'Payment Date', 'Method', 'Transaction ID',
            'Platform Fee', 'Net Amount', 'Remarks',
        ]);

        $this->paymentsQuery()
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($out): void {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        optional($row->tenant)->name,
                        optional($row->user)->name,
                        optional($row->user)->email,
                        optional($row->user)->phone,
                        (float) $row->amount,
                        $row->status,
                        $row->payment_date,
                        $row->payment_method,
                        $row->transaction_id,
                        (float) ($row->platform_fee_amount ?? 0),
                        (float) ($row->net_amount ?? 0),
                        $row->remarks,
                    ]);
                }
            });
    }

    private function streamInvoices($out): void
    {
        fputcsv($out, [
            'Invoice ID', 'Invoice No', 'Tenant', 'Plan', 'Amount', 'Currency',
            'Status', 'Due Date', 'Payment Method', 'Paid At', 'Email Sent At',
            'Email Attempts', 'Created At',
        ]);

        $this->invoicesQuery()
            ->orderBy('id')
            ->chunk(1000, function ($rows) use ($out): void {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->invoice_no,
                        optional($row->tenant)->name,
                        optional($row->plan)->name,
                        (float) $row->amount,
                        $row->currency_code,
                        $row->status,
                        optional($row->due_date)->toDateString(),
                        $row->payment_method,
                        optional($row->paid_at)->toDateTimeString(),
                        optional($row->receipt_emailed_at)->toDateTimeString(),
                        (int) $row->receipt_email_attempts,
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });
    }

    private function studentsQuery()
    {
        $query = User::query()
            ->where('role', 'student')
            ->with([
                'tenant:id,name',
                'memberships' => function ($q): void {
                    $q->with('tenant:id,name');
                    if ($this->tenantId) {
                        $q->where('tenant_id', $this->tenantId);
                    }
                },
            ]);

        if ($this->tenantId) {
            $query->where(function ($q): void {
                $q->where('tenant_id', $this->tenantId)
                    ->orWhereHas('memberships', fn ($m) => $m->where('tenant_id', $this->tenantId));
            });
        }

        if ($this->status !== '') {
            $query->whereHas('memberships', function ($q): void {
                if ($this->tenantId) {
                    $q->where('tenant_id', $this->tenantId);
                }
                $q->where('status', $this->status);
            });
        }

        $this->applyDateFilter($query, 'created_at');
        $this->applyUserSearchFilter($query);

        return $query;
    }

    private function attendanceQuery()
    {
        $query = StudentAttendance::query()->with(['tenant:id,name', 'user:id,name,email,phone']);

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $this->applyDateFilter($query, 'date');
        $this->applyUserSearchFilter($query, 'user');

        return $query;
    }

    private function leavesQuery()
    {
        $query = StudentLeave::query()->with(['tenant:id,name', 'user:id,name,email,phone']);

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $this->applyDateFilter($query, 'start_date');
        $this->applyUserSearchFilter($query, 'user');

        return $query;
    }

    private function paymentsQuery()
    {
        $query = FeePayment::query()->with(['tenant:id,name', 'user:id,name,email,phone']);

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $this->applyDateFilter($query, 'payment_date');
        $this->applyUserSearchFilter($query, 'user');

        return $query;
    }

    private function invoicesQuery()
    {
        $query = TenantSubscriptionInvoice::query()->with(['tenant:id,name', 'plan:id,name']);

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
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

    private function applyUserSearchFilter($query, string $relation = ''): void
    {
        if (trim($this->search) === '') {
            return;
        }

        $search = trim($this->search);

        if ($relation === '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });

            return;
        }

        $query->whereHas($relation, function ($q) use ($search): void {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
