<?php

namespace App\Livewire\Admin;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SupportTickets extends Component
{
    use WithPagination;

    public ?SupportTicket $selectedTicket = null;
    public string $replyMessage = '';
    public string $filterStatus = 'all';
    public string $filterPriority = 'all';

    public function selectTicket(int $id): void
    {
        $this->selectedTicket = SupportTicket::with(['user', 'replies.user'])->findOrFail($id);
        // Mark as answered once an admin views and opens a ticket
        if ($this->selectedTicket->status === 'open') {
            $this->selectedTicket->update(['status' => 'answered']);
        }
    }

    public function sendReply(): void
    {
        $this->validate(['replyMessage' => 'required|string|min:5']);

        if (!$this->selectedTicket)
            return;

        SupportTicketReply::create([
            'support_ticket_id' => $this->selectedTicket->id,
            'user_id' => Auth::id(),
            'message' => $this->replyMessage,
        ]);

        $this->replyMessage = '';
        // Refresh the ticket with its replies
        $this->selectTicket($this->selectedTicket->id);

        $this->dispatch('notify', type: 'success', message: 'Reply sent successfully.');
    }

    public function closeTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        $this->selectedTicket->update(['status' => 'closed']);
        $this->selectTicket($this->selectedTicket->id);
    }

    public function reopenTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        $this->selectedTicket->update(['status' => 'open']);
        $this->selectTicket($this->selectedTicket->id);
    }

    public function deleteTicket(): void
    {
        if (!$this->selectedTicket)
            return;
        $this->selectedTicket->delete();
        $this->selectedTicket = null;
    }

    public function render()
    {
        $query = SupportTicket::with('user')
            ->withCount('replies')
            ->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority !== 'all') {
            $query->where('priority', $this->filterPriority);
        }

        return view('livewire.admin.support-tickets', [
            'tickets' => $query->paginate(10),
        ])->layout('layouts.app', [
            'header' => 'Support Tickets',
        ]);
    }
}