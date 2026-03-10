<?php

namespace App\Livewire\Admin;

use App\Models\FeePayment;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use App\Models\StudentSubscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentProfile extends Component
{
    public User $student;
    public array $stats = [];

    public function mount(User $student): void
    {
        abort_unless($student->role === 'student', 404);

        $this->student = $student->load([
            'tenant',
            'memberships.tenant',
        ]);

        $this->stats = [
            'memberships' => $this->student->memberships()->count(),
            'active_subscriptions' => StudentSubscription::query()
                ->where('user_id', $this->student->id)
                ->where('status', 'active')
                ->count(),
            'total_payments' => (float) FeePayment::query()
                ->where('user_id', $this->student->id)
                ->where('status', 'paid')
                ->sum('amount'),
            'present_days_30' => StudentAttendance::query()
                ->where('user_id', $this->student->id)
                ->where('status', 'present')
                ->whereDate('date', '>=', now()->subDays(30)->toDateString())
                ->count(),
        ];
    }

    public function render()
    {
        $subscriptions = StudentSubscription::query()
            ->with(['tenant:id,name', 'plan:id,name,price', 'seat:id,name'])
            ->where('user_id', $this->student->id)
            ->latest()
            ->take(10)
            ->get();

        $payments = FeePayment::query()
            ->with('tenant:id,name')
            ->where('user_id', $this->student->id)
            ->latest('payment_date')
            ->take(10)
            ->get();

        $attendances = StudentAttendance::query()
            ->with('tenant:id,name')
            ->where('user_id', $this->student->id)
            ->latest('date')
            ->take(10)
            ->get();

        $leaves = StudentLeave::query()
            ->with('tenant:id,name')
            ->where('user_id', $this->student->id)
            ->latest('start_date')
            ->take(10)
            ->get();

        return view('livewire.admin.student-profile', [
            'subscriptions' => $subscriptions,
            'payments' => $payments,
            'attendances' => $attendances,
            'leaves' => $leaves,
        ]);
    }
}

