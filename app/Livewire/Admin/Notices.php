<?php

namespace App\Livewire\Admin;

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
    public string $audience = 'both';
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
            'audience' => 'required|in:students,libraries,both',
            'deliveryInApp' => 'boolean',
            'deliveryEmail' => 'boolean',
            'deliveryPush' => 'boolean',
            'deliveryWhatsapp' => 'boolean',
            'isActive' => 'boolean',
            'startsAt' => 'nullable|date',
            'endsAt' => 'nullable|date|after_or_equal:startsAt',
        ]);

        $notice = Notice::create([
            'tenant_id' => null,
            'created_by' => Auth::id(),
            'title' => trim($validated['title']),
            'body' => trim($validated['body']),
            'level' => $validated['level'],
            'audience' => $validated['audience'],
            'delivery_in_app' => $validated['deliveryInApp'],
            'delivery_email' => $validated['deliveryEmail'],
            'delivery_push' => $validated['deliveryPush'],
            'delivery_whatsapp' => $validated['deliveryWhatsapp'],
            'is_active' => $validated['isActive'],
            'starts_at' => $validated['startsAt'] ?: null,
            'ends_at' => $validated['endsAt'] ?: null,
        ]);

        $sentCount = app(NoticeDispatchService::class)->dispatch($notice);
        session()->flash('message', "Notice published successfully to {$sentCount} user(s).");

        $this->reset(['title', 'body', 'startsAt', 'endsAt']);
        $this->level = 'info';
        $this->audience = 'both';
        $this->deliveryInApp = true;
        $this->deliveryEmail = false;
        $this->deliveryPush = false;
        $this->deliveryWhatsapp = false;
        $this->isActive = true;
    }

    public function render()
    {
        return view('livewire.admin.notices', [
            'notices' => Notice::query()
                ->with('creator')
                ->latest()
                ->paginate(10),
        ]);
    }
}
