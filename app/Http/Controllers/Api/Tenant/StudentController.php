<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSortBy = ['id', 'name', 'email', 'created_at', 'updated_at'];

        if (! in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'created_at';
        }

        $students = User::where('tenant_id', $tenantId)
            ->where('role', 'student')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($students);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => 'required|string|min:8',
        ]);

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'tenant_id' => $tenantId,
        ]);

        return response()->json($student, 201);
    }

    public function show(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $student = User::where('tenant_id', $tenantId)->where('role', 'student')->findOrFail($id);

        return response()->json($student);
    }

    public function update(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $student = User::where('tenant_id', $tenantId)->where('role', 'student')->findOrFail($id);
        $before = $student->only(['id', 'name', 'email', 'phone', 'tenant_id', 'role']);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'password' => 'sometimes|required|string|min:8',
        ]);

        if (array_key_exists('password', $validated)) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $student->update($validated);
        $student->refresh();

        app(AuditLogService::class)->log(
            action: 'student.updated',
            entityType: User::class,
            entityId: $student->id,
            oldValues: $before,
            newValues: $student->only(['id', 'name', 'email', 'phone', 'tenant_id', 'role']),
            actor: $request->user(),
            tenantId: $tenantId,
            request: $request
        );

        return response()->json($student);
    }

    public function destroy(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $student = User::where('tenant_id', $tenantId)->where('role', 'student')->findOrFail($id);

        app(AuditLogService::class)->log(
            action: 'student.deleted',
            entityType: User::class,
            entityId: $student->id,
            oldValues: $student->only(['id', 'name', 'email', 'phone', 'tenant_id', 'role']),
            actor: $request->user(),
            tenantId: $tenantId,
            request: $request
        );

        $student->delete();

        return response()->json(['message' => 'Student removed']);
    }
}
