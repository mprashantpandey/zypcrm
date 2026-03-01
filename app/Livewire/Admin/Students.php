<?php

namespace App\Livewire\Admin;

use App\Models\StudentMembership;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Students extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $isMembershipModalOpen = false;
    public ?int $membershipStudentId = null;
    public string $membershipStudentName = '';
    public array $membershipTenantIds = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openMembershipManager(int $studentId): void
    {
        $student = User::query()
            ->where('role', 'student')
            ->with('memberships')
            ->findOrFail($studentId);

        $this->membershipStudentId = $student->id;
        $this->membershipStudentName = $student->name;
        $this->membershipTenantIds = $student->memberships
            ->pluck('tenant_id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        $this->isMembershipModalOpen = true;
    }

    public function closeMembershipManager(): void
    {
        $this->reset(['isMembershipModalOpen', 'membershipStudentId', 'membershipStudentName', 'membershipTenantIds']);
    }

    public function syncMemberships(): void
    {
        $validated = $this->validate([
            'membershipStudentId' => ['required', 'exists:users,id'],
            'membershipTenantIds' => ['required', 'array', 'min:1'],
            'membershipTenantIds.*' => ['required', 'integer', 'exists:tenants,id'],
        ]);

        $student = User::query()
            ->where('id', $validated['membershipStudentId'])
            ->where('role', 'student')
            ->firstOrFail();

        $tenantIds = collect($validated['membershipTenantIds'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($tenantIds as $tenantId) {
            StudentMembership::updateOrCreate(
                ['user_id' => $student->id, 'tenant_id' => $tenantId],
                ['status' => 'active', 'joined_at' => now()]
            );
        }

        StudentMembership::query()
            ->where('user_id', $student->id)
            ->whereNotIn('tenant_id', $tenantIds->all())
            ->delete();

        // Keep backward compatibility with legacy tenant_id usage.
        $student->update(['tenant_id' => $tenantIds->first()]);

        session()->flash('message', 'Student memberships updated successfully.');
        $this->closeMembershipManager();
    }

    public function render()
    {
        $students = User::query()
            ->where('role', 'student')
            ->with(['memberships.tenant'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(15);

        $tenants = Tenant::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.students', [
            'students' => $students,
            'tenants' => $tenants,
        ]);
    }
}
