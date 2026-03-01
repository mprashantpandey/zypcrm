<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Tenant;

#[Layout('layouts.app')]
class Tenants extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'All';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function toggleStatus($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->status = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->save();
        
        session()->flash('message', 'Tenant status updated successfully.');
    }

    public function render()
    {
        $query = Tenant::query()->with(['users' => function($q) {
            $q->where('role', 'library_owner');
        }, 'currentSubscription.plan']);

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('users', function($q2) {
                      $q2->where('email', 'like', '%' . $this->search . '%')
                        ->where('role', 'library_owner');
                  });
            });
        }

        if ($this->statusFilter !== 'All') {
            $query->where([['status', '=', strtolower($this->statusFilter)]]);
        }

        $tenants = $query->latest()->paginate(10);

        return view('livewire.admin.tenants', compact('tenants'));
    }
}