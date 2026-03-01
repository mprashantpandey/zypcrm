<?php

namespace App\Livewire\Student\Concerns;

use Illuminate\Support\Facades\Auth;

trait ResolvesActiveTenant
{
    protected function getStudentMemberships()
    {
        return Auth::user()
            ->memberships()
            ->with('tenant')
            ->where('status', 'active')
            ->orderByDesc('joined_at')
            ->get();
    }

    protected function resolveActiveTenantId(): ?int
    {
        $membershipTenantIds = $this->getStudentMemberships()->pluck('tenant_id')->map(fn ($id) => (int) $id)->all();
        if (empty($membershipTenantIds)) {
            return null;
        }

        $sessionTenantId = (int) session('student_active_tenant_id');
        if ($sessionTenantId && in_array($sessionTenantId, $membershipTenantIds, true)) {
            return $sessionTenantId;
        }

        $defaultTenantId = (int) Auth::user()->tenant_id;
        if (! in_array($defaultTenantId, $membershipTenantIds, true)) {
            $defaultTenantId = $membershipTenantIds[0];
        }

        session(['student_active_tenant_id' => $defaultTenantId]);

        return $defaultTenantId;
    }

    public function switchTenantContext(int $tenantId): void
    {
        $membershipTenantIds = $this->getStudentMemberships()->pluck('tenant_id')->map(fn ($id) => (int) $id)->all();
        if (! in_array($tenantId, $membershipTenantIds, true)) {
            return;
        }

        session(['student_active_tenant_id' => $tenantId]);
    }
}

