<?php

namespace App\Livewire\Student;

use App\Livewire\Student\Concerns\ResolvesActiveTenant;
use App\Services\NoticeDispatchService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notices extends Component
{
    use WithPagination;
    use ResolvesActiveTenant;

    public ?int $activeTenantId = null;

    public function mount(): void
    {
        $this->activeTenantId = $this->resolveActiveTenantId();
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        $this->activeTenantId = $this->resolveActiveTenantId();
        $student = Auth::user();

        $notices = app(NoticeDispatchService::class)
            ->visibleNoticesFor($student, $this->activeTenantId)
            ->with('creator', 'tenant')
            ->paginate(10);

        $notifications = $student->notifications()
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.student.notices', [
            'notices' => $notices,
            'notifications' => $notifications,
            'activeTenant' => $this->getStudentMemberships()->firstWhere('tenant_id', $this->activeTenantId)?->tenant,
            'unreadCount' => $student->unreadNotifications()->count(),
        ])->layout('layouts.app', [
            'header' => 'Notices & Notifications',
        ]);
    }
}

