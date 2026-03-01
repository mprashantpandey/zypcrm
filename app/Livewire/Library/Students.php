<?php

namespace App\Livewire\Library;

use App\Models\FeePayment;
use App\Models\LibraryPlan;
use App\Models\Seat;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use App\Models\StudentMembership;
use App\Models\StudentSubscription;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;

    public $search = '';

    public $isModalOpen = false;

    public $isProfileModalOpen = false;

    // Profile Modal properties
    public $profileStudent = null;

    public $profileAttendanceStats = [];

    public $profileRecentLeaves = [];

    // Form fields
    public $studentId = null;

    public $name = '';

    public $email = '';

    public $phone = '';

    public $password = '';

    public $assignedSeatId = '';

    public $libraryPlanId = '';

    public $planStartDate = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->studentId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->assignedSeatId = '';
        $this->libraryPlanId = '';
        $this->planStartDate = date('Y-m-d');
    }

    public function viewProfile($studentId)
    {
        $tenantId = Auth::user()->tenant_id;

        $this->profileStudent = User::with(['assignedSeat', 'activeSubscription.plan'])
            ->whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
            ->findOrFail($studentId);

        // Get attendance stats for this month
        $this->profileAttendanceStats = [
            'present' => StudentAttendance::where('tenant_id', $tenantId)
                ->where('user_id', $studentId)
                ->whereMonth('date', date('m'))
                ->where('status', 'present')
                ->count(),
            'absent' => StudentAttendance::where('tenant_id', $tenantId)
                ->where('user_id', $studentId)
                ->whereMonth('date', date('m'))
                ->where('status', 'absent')
                ->count(),
            'leave' => StudentAttendance::where('tenant_id', $tenantId)
                ->where('user_id', $studentId)
                ->whereMonth('date', date('m'))
                ->where('status', 'leave')
                ->count(),
        ];

        $this->profileRecentLeaves = StudentLeave::where('tenant_id', $tenantId)
            ->where('user_id', $studentId)
            ->latest()
            ->take(5)
            ->get();

        $this->isProfileModalOpen = true;
    }

    public function closeProfileModal()
    {
        $this->isProfileModalOpen = false;
        $this->profileStudent = null;
    }

    public function edit($id)
    {
        $student = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', Auth::user()->tenant_id))
            ->where('role', 'student')
            ->findOrFail($id);

        $this->studentId = $student->id;
        $this->name = $student->name;
        $this->email = $student->email;
        $this->phone = $student->phone;

        // Find if they have an assigned seat
        $seat = Seat::where('tenant_id', Auth::user()->tenant_id)
            ->where('user_id', $student->id)
            ->first();
        if ($seat) {
            $this->assignedSeatId = $seat->id;
        } else {
            $this->assignedSeatId = '';
        }

        // Find active subscription
        $subscription = StudentSubscription::where('user_id', $student->id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if ($subscription) {
            $this->libraryPlanId = $subscription->library_plan_id;
            $this->planStartDate = $subscription->start_date->format('Y-m-d');
        } else {
            $this->libraryPlanId = '';
            $this->planStartDate = date('Y-m-d');
        }

        $this->isModalOpen = true;
    }

    public function save()
    {
        $tenantId = Auth::user()->tenant_id;
        $actor = Auth::user();
        $isUpdate = (bool) $this->studentId;
        $before = [];

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->studentId)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($this->studentId)],
            'assignedSeatId' => ['nullable', Rule::exists('seats', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'libraryPlanId' => ['nullable', Rule::exists('library_plans', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId))],
            'planStartDate' => 'nullable|date',
        ];

        if (! $this->studentId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules);

        $studentData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => 'student',
        ];

        if (! $this->studentId && ! empty($this->password)) {
            // New student creation
            $studentData['password'] = Hash::make($this->password);
        }

        if ($this->studentId) {
            $student = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
                ->where('role', 'student')
                ->findOrFail($this->studentId);
            $before = $student->only(['id', 'name', 'email', 'phone', 'role']);
            $student->update($studentData);
        } else {
            $existingStudent = User::query()
                ->where('role', 'student')
                ->where(function ($query) {
                    $query->where('phone', $this->phone);
                    if (! empty($this->email)) {
                        $query->orWhere('email', $this->email);
                    }
                })
                ->first();

            if ($existingStudent) {
                $student = $existingStudent;
                $student->update($studentData);
                if (! empty($this->password)) {
                    $student->update(['password' => Hash::make($this->password)]);
                }
            } else {
                $studentData['tenant_id'] = $tenantId;
                $student = User::create($studentData);
            }
        }

        StudentMembership::updateOrCreate(
            ['user_id' => $student->id, 'tenant_id' => $tenantId],
            ['status' => 'active', 'joined_at' => now()]
        );

        // Only update password for existing users if provided
        if ($this->studentId && ! empty($this->password)) {
            $student->update(['password' => Hash::make($this->password)]);
        }

        // Handle seat assignment
        // First, unassign any previous seat this student had
        Seat::where('tenant_id', $tenantId)
            ->where('user_id', $student->id)
            ->update(['user_id' => null, 'status' => 'available']);

        if (! empty($this->assignedSeatId)) {
            // Assign the new seat
            Seat::where('id', $this->assignedSeatId)
                ->where('tenant_id', $tenantId)
                ->update(['user_id' => $student->id, 'status' => 'occupied']);
        }

        // Handle Subscription Assignment
        if (! empty($this->libraryPlanId)) {
            $plan = LibraryPlan::where('tenant_id', $tenantId)->find($this->libraryPlanId);
            if ($plan) {
                // Determine start date, default to today if not provided
                $startDate = empty($this->planStartDate) ? Carbon::today() : Carbon::parse($this->planStartDate);
                // Calculate end date based on plan's validity period
                $endDate = $startDate->copy()->addDays($plan->duration_days);

                $existingSub = StudentSubscription::where('user_id', $student->id)
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'active')
                    ->first();

                if ($existingSub) {
                    $oldPlanId = $existingSub->library_plan_id;

                    // Update existing active subscription
                    $existingSub->update([
                        'library_plan_id' => $this->libraryPlanId,
                        'seat_id' => $this->assignedSeatId ?: null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);

                    // If the plan changed, we should probably issue a new invoice for the newly selected plan
                    // For simplicity, we'll assume any new assignment (even if it's the same plan ID but they clicked Save)
                    // doesn't duplicate invoices unless the plan actually changed, or if it's a renewal.
                    // To be safe against double-billing, we only generate a new invoice if the plan changes.
                    if ($oldPlanId != $this->libraryPlanId) {
                        FeePayment::create([
                            'tenant_id' => $tenantId,
                            'user_id' => $student->id,
                            'amount' => $plan->price,
                            'payment_date' => Carbon::today(),
                            'status' => 'pending',
                            'payment_method' => 'cash', // Default placeholder
                            'platform_fee_amount' => 0,
                            'net_amount' => $plan->price,
                            'remarks' => 'Automated invoice for '.$plan->name.' plan change.',
                        ]);
                    }
                } else {
                    // Create new subscription
                    StudentSubscription::create([
                        'tenant_id' => $tenantId,
                        'user_id' => $student->id,
                        'library_plan_id' => $this->libraryPlanId,
                        'seat_id' => $this->assignedSeatId ?: null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => 'active',
                    ]);

                    // Generate Automated Invoice for the new Subscription
                    FeePayment::create([
                        'tenant_id' => $tenantId,
                        'user_id' => $student->id,
                        'amount' => $plan->price,
                        'payment_date' => Carbon::today(),
                        'status' => 'pending',
                        'payment_method' => 'cash', // Default placeholder until paid
                        'platform_fee_amount' => 0,
                        'net_amount' => $plan->price,
                        'remarks' => 'Automated invoice for '.$plan->name.' subscription.',
                    ]);
                }
            }
        } else {
            // If plan is deselected, cancel active subscription
            StudentSubscription::where('user_id', $student->id)
                ->where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->update(['status' => 'canceled']);
        }

        if ($isUpdate) {
            app(AuditLogService::class)->log(
                action: 'student.updated',
                entityType: User::class,
                entityId: $student->id,
                oldValues: $before,
                newValues: $student->fresh()->only(['id', 'name', 'email', 'phone', 'tenant_id', 'role']),
                actor: $actor,
                tenantId: $tenantId,
                request: request()
            );
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => $this->studentId ? 'Student updated successfully.' : 'Student created successfully.']);
        $this->closeModal();
    }

    public function delete($id)
    {
        $student = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', Auth::user()->tenant_id))
            ->where('role', 'student')
            ->findOrFail($id);
        $tenantId = Auth::user()->tenant_id;

        // Unassign seat
        Seat::where('tenant_id', $tenantId)
            ->where('user_id', $student->id)
            ->update(['user_id' => null, 'status' => 'available']);

        app(AuditLogService::class)->log(
            action: 'student.deleted',
            entityType: User::class,
            entityId: $student->id,
            oldValues: $student->only(['id', 'name', 'email', 'phone', 'tenant_id', 'role']),
            actor: Auth::user(),
            tenantId: $tenantId,
            request: request()
        );

        StudentMembership::where('tenant_id', $tenantId)
            ->where('user_id', $student->id)
            ->delete();

        if (! StudentMembership::where('user_id', $student->id)->exists()) {
            $student->delete();
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Student deleted successfully.']);
    }

    public function exportCSV()
    {
        $tenantId = Auth::user()->tenant_id;

        $students = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('role', 'student')
            ->with(['activeSubscription.plan', 'assignedSeat'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                }
                );
            })
            ->latest()
            ->get();

        $csvHeaders = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=students_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Name', 'Phone', 'Email', 'Active Plan', 'Seat', 'Joined Date'];

        $callback = function () use ($students, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                $row['ID'] = str_pad($student->id, 5, '0', STR_PAD_LEFT);
                $row['Name'] = $student->name;
                $row['Phone'] = "'".$student->phone; // Prevent Excel from stripping leading zeros
                $row['Email'] = $student->email;
                $row['Plan'] = $student->activeSubscription?->plan?->name ?: 'No Plan';
                $row['Seat'] = $student->assignedSeat?->name ?: 'Unassigned';
                $row['Joined Date'] = $student->created_at->format('Y-m-d');

                fputcsv($file, [$row['ID'], $row['Name'], $row['Phone'], $row['Email'], $row['Plan'], $row['Seat'], $row['Joined Date']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $csvHeaders);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $students = User::whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('role', 'student')
            ->with(['activeSubscription.plan', 'assignedSeat'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                }
                );
            })
            ->latest()
            ->paginate(10);

        // Get available seats for the dropdown
        $availableSeats = Seat::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->whereNull('user_id');
                if ($this->studentId) {
                    $q->orWhere('user_id', $this->studentId);
                }
            })
            ->orderBy('name')
            ->get();

        // Get available plans for dropdown
        $libraryPlans = LibraryPlan::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('livewire.library.students', [
            'students' => $students,
            'availableSeats' => $availableSeats,
            'libraryPlans' => $libraryPlans,
        ])->layout('layouts.app', [
            'header' => 'Student Management',
        ]);
    }
}
