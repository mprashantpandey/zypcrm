<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeatController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSortBy = ['id', 'name', 'status', 'created_at', 'updated_at'];

        if (! in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'created_at';
        }

        $seats = Seat::where('tenant_id', $tenantId)
            ->with('student')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($seats);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|in:available,occupied,maintenance',
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->where('role', 'student')),
            ],
        ]);

        $seat = Seat::create(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json($seat->load('student'), 201);
    }

    public function show(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $seat = Seat::where('tenant_id', $tenantId)->with('student')->findOrFail($id);

        return response()->json($seat);
    }

    public function update(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $seat = Seat::where('tenant_id', $tenantId)->findOrFail($id);
        $before = $seat->only(['id', 'tenant_id', 'name', 'status', 'user_id']);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:available,occupied,maintenance',
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->where('role', 'student')),
            ],
        ]);

        $seat->update($validated);
        $seat->refresh();

        if (($before['user_id'] ?? null) && is_null($seat->user_id)) {
            app(AuditLogService::class)->log(
                action: 'seat.unassigned',
                entityType: Seat::class,
                entityId: $seat->id,
                oldValues: $before,
                newValues: $seat->only(['id', 'tenant_id', 'name', 'status', 'user_id']),
                actor: $request->user(),
                tenantId: $tenantId,
                request: $request
            );
        }

        return response()->json($seat->load('student'));
    }

    public function destroy(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $seat = Seat::where('tenant_id', $tenantId)->findOrFail($id);
        $seat->delete();

        return response()->json(['message' => 'Seat removed']);
    }
}
