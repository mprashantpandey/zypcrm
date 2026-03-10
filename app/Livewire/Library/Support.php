<?php

namespace App\Livewire\Library;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Support extends Component
{
    public string $subject = '';
    public string $priority = 'medium';

    public function submit(): void
    {
        if (! \App\Models\Setting::getBool('enable_support_tickets', false)) {
            $this->addError('subject', 'Support tickets are currently disabled by the platform.');
            return;
        }

        $key = 'support-ticket:'.Auth::id().':'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('subject', 'You have created too many tickets recently. Please wait a while before trying again.');
            return;
        }

        $this->validate([
            'subject' => 'required|string|min:8|max:255',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'priority' => $this->priority,
        ]);

        RateLimiter::hit($key, now()->addMinutes(30));

        $this->reset(['subject', 'priority']);
        $this->priority = 'medium';

        $this->dispatch('notify', type: 'success', message: 'Support ticket created. Our team will respond soon.');
    }

    public function render()
    {
        if (! \App\Models\Setting::getBool('enable_support_tickets', false)) {
            return view('livewire.library.support-disabled', [
                'tickets' => collect(),
            ])->layout('layouts.app', [
                'header' => 'Support',
            ]);
        }

        $tickets = SupportTicket::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.library.support', [
            'tickets' => $tickets,
        ])->layout('layouts.app', [
            'header' => 'Support',
        ]);
    }
}

