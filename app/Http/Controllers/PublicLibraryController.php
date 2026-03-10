<?php

namespace App\Http\Controllers;

use App\Models\LibraryLead;
use App\Models\LibraryPlan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicLibraryController extends Controller
{
    public function show(string $slug): View
    {
        $tenantQuery = Tenant::query()->where('status', 'active');
        $hasPublicColumns = Schema::hasColumn('tenants', 'public_slug')
            && Schema::hasColumn('tenants', 'public_page_enabled');

        if ($hasPublicColumns) {
            $tenant = $tenantQuery
                ->where('public_slug', $slug)
                ->where('public_page_enabled', true)
                ->firstOrFail();
        } else {
            $tenant = $tenantQuery->get()->first(function ($item) use ($slug) {
                return Str::slug((string) $item->name) === $slug;
            });
            abort_if(! $tenant, 404);
        }

        $plans = LibraryPlan::query()
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        $images = Schema::hasTable('tenant_images')
            ? $tenant->images()->where('is_active', true)->get()
            : collect();

        return view('public.library-page', [
            'tenant' => $tenant,
            'plans' => $plans,
            'images' => $images,
        ]);
    }

    public function submitLead(Request $request, string $slug): RedirectResponse
    {
        $tenantQuery = Tenant::query()->where('status', 'active');
        $hasPublicColumns = Schema::hasColumn('tenants', 'public_slug')
            && Schema::hasColumn('tenants', 'public_page_enabled');

        if ($hasPublicColumns) {
            $tenant = $tenantQuery
                ->where('public_slug', $slug)
                ->where('public_page_enabled', true)
                ->firstOrFail();
        } else {
            $tenant = $tenantQuery->get()->first(function ($item) use ($slug) {
                return Str::slug((string) $item->name) === $slug;
            });
            abort_if(! $tenant, 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'message' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'string', 'max:5'],
            'form_started_at' => ['nullable', 'integer'],
        ]);

        $startedAt = (int) ($validated['form_started_at'] ?? 0);
        $tooFast = $startedAt > 0 && (time() - $startedAt) < 2;
        $honeypotTriggered = ! empty($validated['website']) || $tooFast;
        if ($honeypotTriggered) {
            // Return success response to avoid giving bots useful signal.
            return back()->with('success', 'Thanks, your inquiry has been submitted. Library team will contact you soon.');
        }

        if (! Schema::hasTable('library_leads')) {
            return back()->withErrors([
                'name' => 'Lead form is not available yet. Please try again in a moment.',
            ])->withInput();
        }

        LibraryLead::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'message' => $validated['message'] ?? null,
            'source' => 'public_page',
            'status' => 'new',
        ]);

        return back()->with('success', 'Thanks, your inquiry has been submitted. Library team will contact you soon.');
    }
}
