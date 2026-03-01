<?php

namespace App\Livewire\Library;

use App\Models\FeePayment;
use App\Models\LibraryPlan;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Fees extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = 'all';

    public $fromDate = '';

    public $toDate = '';

    public $isModalOpen = false;

    public $paymentId = null;

    public $user_id = '';

    public $library_plan_id = '';

    public $amount = '';

    public $payment_date = '';

    public $status = 'paid';

    public $payment_method = 'cash';

    public $transaction_id = '';

    public $remarks = '';

    public function updatedLibraryPlanId($planId)
    {
        if ($planId) {
            $plan = LibraryPlan::where('tenant_id', Auth::user()->tenant_id)->find($planId);
            if ($plan) {
                $this->amount = $plan->price;
            } else {
                $this->amount = '';
            }
        } else {
            $this->amount = '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFromDate()
    {
        $this->resetPage();
    }

    public function updatingToDate()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterStatus = 'all';
        $this->fromDate = '';
        $this->toDate = '';
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->payment_date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->paymentId = null;
        $this->user_id = '';
        $this->library_plan_id = '';
        $this->amount = '';
        $this->payment_date = '';
        $this->status = 'paid';
        $this->payment_method = 'cash';
        $this->transaction_id = '';
        $this->remarks = '';
    }

    public function edit($id)
    {
        $payment = FeePayment::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $this->paymentId = $payment->id;
        $this->user_id = $payment->user_id;
        $this->amount = $payment->amount;
        $this->payment_date = $payment->payment_date;
        $this->status = $payment->status;
        $this->payment_method = $payment->payment_method;
        $this->transaction_id = $payment->transaction_id;
        $this->remarks = $payment->remarks;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'user_id' => 'required',
            'library_plan_id' => 'required_without:paymentId',
            'payment_date' => 'required|date',
            'status' => 'required|string|in:paid,pending,overdue',
            'payment_method' => 'required|string|in:cash,online',
            'transaction_id' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ]);

        $tenantId = Auth::user()->tenant_id;

        // Verify the student belongs to this tenant
        $studentExists = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('id', $this->user_id)
            ->where('role', 'student')
            ->exists();

        if (! $studentExists) {
            $this->addError('user_id', 'Selected student does not belong to your library.');

            return;
        }

        if (! $this->paymentId) {
            // Creating a new strict invoice
            $plan = LibraryPlan::where('tenant_id', $tenantId)->find($this->library_plan_id);
            if (! $plan) {
                $this->addError('library_plan_id', 'Invalid library plan selected.');

                return;
            }

            $payment = FeePayment::create([
                'tenant_id' => $tenantId,
                'user_id' => $this->user_id,
                'amount' => $plan->price,
                'payment_date' => $this->payment_date,
                'status' => $this->status,
                'payment_method' => $this->payment_method,
                'platform_fee_amount' => 0,
                'net_amount' => $plan->price,
                'transaction_id' => $this->transaction_id,
                'remarks' => trim($this->remarks.' (Manual invoice for '.$plan->name.')'),
            ]);

            if ($payment->status === 'paid' && $payment->user) {
                $payment->user->notify(new \App\Notifications\FeePaymentReceipt($payment));
            }
        } else {
            // Updating existing
            $payment = FeePayment::where('tenant_id', $tenantId)->findOrFail($this->paymentId);
            $wasPaid = $payment->status === 'paid';
            $before = $payment->only([
                'id',
                'tenant_id',
                'user_id',
                'amount',
                'payment_date',
                'status',
                'payment_method',
                'transaction_id',
                'remarks',
            ]);

            $payment->update([
                'payment_date' => $this->payment_date,
                'status' => $this->status,
                'payment_method' => $this->payment_method,
                'transaction_id' => $this->transaction_id,
                'remarks' => $this->remarks,
            ]);

            app(AuditLogService::class)->log(
                action: 'fee_payment.updated',
                entityType: FeePayment::class,
                entityId: $payment->id,
                oldValues: $before,
                newValues: $payment->fresh()->only([
                    'id',
                    'tenant_id',
                    'user_id',
                    'amount',
                    'payment_date',
                    'status',
                    'payment_method',
                    'transaction_id',
                    'remarks',
                ]),
                actor: Auth::user(),
                tenantId: $tenantId,
                request: request()
            );

            if (! $wasPaid && $payment->status === 'paid' && $payment->user) {
                $payment->user->notify(new \App\Notifications\FeePaymentReceipt($payment));
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->paymentId ? 'Payment updated successfully.' : 'Invoice created successfully.',
        ]);
        $this->closeModal();
    }

    public function delete($id)
    {
        $payment = FeePayment::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $payment->delete();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Payment record deleted successfully.',
        ]);
    }

    public function exportCSV()
    {
        $tenantId = Auth::user()->tenant_id;

        $payments = $this->buildPaymentsQuery($tenantId)
            ->with('user')
            ->orderBy('payment_date', 'desc')
            ->get();

        $csvHeaders = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=fees_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Date', 'Student', 'Amount', 'Status', 'Method', 'Remarks'];

        $callback = function () use ($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($payments as $payment) {
                $row['ID'] = $payment->id;
                $row['Date'] = Carbon::parse($payment->payment_date)->format('Y-m-d');
                $row['Student'] = $payment->user ? $payment->user->name : 'N/A';
                $row['Amount'] = $payment->amount;
                $row['Status'] = ucfirst($payment->status);
                $row['Method'] = ucfirst($payment->payment_method);
                $row['Remarks'] = $payment->remarks;
                fputcsv($file, [$row['ID'], $row['Date'], $row['Student'], $row['Amount'], $row['Status'], $row['Method'], $row['Remarks']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $csvHeaders);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $payments = $this->buildPaymentsQuery($tenantId)
            ->with('user')
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        // Fetch students for the dropdown
        $students = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);

        $libraryPlans = LibraryPlan::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get(['id', 'name', 'price']);

        $stats = [
            'total_collected' => FeePayment::where('tenant_id', $tenantId)->where('status', 'paid')->sum('amount'),
            'pending_amount' => FeePayment::where('tenant_id', $tenantId)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'this_month' => FeePayment::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->whereMonth('payment_date', date('m'))
                ->whereYear('payment_date', date('Y'))
                ->sum('amount'),
        ];

        return view('livewire.library.fees', [
            'payments' => $payments,
            'students' => $students,
            'libraryPlans' => $libraryPlans,
            'stats' => $stats,
        ])->layout('layouts.app', [
            'header' => 'Fee Collection',
        ]);
    }

    private function buildPaymentsQuery(int $tenantId)
    {
        return FeePayment::where('tenant_id', $tenantId)
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when(! empty($this->fromDate), function ($query) {
                $query->whereDate('payment_date', '>=', $this->fromDate);
            })
            ->when(! empty($this->toDate), function ($query) {
                $query->whereDate('payment_date', '<=', $this->toDate);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            });
    }
}
