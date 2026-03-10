<?php

namespace App\Livewire\Library;

use App\Models\Notice;
use App\Services\NoticeDispatchService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Notices extends Component
{
    use WithPagination;

    public string $title = '';
    public string $body = '';
    public string $level = 'info';
    public bool $deliveryInApp = true;
    public bool $deliveryEmail = false;
    public bool $deliveryPush = false;
    public bool $deliveryWhatsapp = false;
    public bool $isActive = true;
    public string $startsAt = '';
    public string $endsAt = '';

    public function createNotice(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|min:3|max:200',
            'body' => 'required|string|min:5|max:3000',
            'level' => 'required|in:info,success,warning,critical',
            'deliveryInApp' => 'boolean',
            'deliveryEmail' => 'boolean',
            'deliveryPush' => 'boolean',
            'deliveryWhatsapp' => 'boolean',
            'isActive' => 'boolean',
            'startsAt' => 'nullable|date',
            'endsAt' => 'nullable|date|after_or_equal:startsAt',
        ]);

        $notice = Notice::create([
            'tenant_id' => Auth::user()->tenant_id,
            'created_by' => Auth::id(),
            'title' => trim($validated['title']),
            'body' => trim($validated['body']),
            'level' => $validated['level'],
            'audience' => 'students',
            'delivery_in_app' => $validated['deliveryInApp'],
            'delivery_email' => $validated['deliveryEmail'],
            'delivery_push' => $validated['deliveryPush'],
            'delivery_whatsapp' => $validated['deliveryWhatsapp'],
            'is_active' => $validated['isActive'],
            'starts_at' => $validated['startsAt'] ?: null,
            'ends_at' => $validated['endsAt'] ?: null,
        ]);

        $sentCount = app(NoticeDispatchService::class)->dispatch($notice);
        session()->flash('message', "Notice published successfully to {$sentCount} student(s).");

        $this->reset(['title', 'body', 'startsAt', 'endsAt']);
        $this->level = 'info';
        $this->deliveryInApp = true;
        $this->deliveryEmail = false;
        $this->deliveryPush = false;
        $this->deliveryWhatsapp = false;
        $this->isActive = true;
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $visible = app(NoticeDispatchService::class)
            ->visibleNoticesFor(Auth::user())
            ->with('creator')
            ->paginate(10, ['*'], 'visible');

        $sent = Notice::query()
            ->where('tenant_id', $tenantId)
            ->with('creator')
            ->latest()
            ->paginate(10, ['*'], 'sent');

        return view('livewire.library.notices', [
            'visibleNotices' => $visible,
            'sentNotices' => $sent,
        ]);
    }
}
