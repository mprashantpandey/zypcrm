<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\User;
use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeePaymentController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));
        $sortBy = $request->query('sort_by', 'payment_date');
        $sortDir = $request->query('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSortBy = ['id', 'amount', 'payment_date', 'status', 'created_at', 'updated_at'];

        if (! in_array($sortBy, $allowedSortBy, true)) {
            $sortBy = 'payment_date';
        }

        $payments = FeePayment::where('tenant_id', $tenantId)
            ->with('student')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $tenantId = $request->user()->tenant_id;
        $validated = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'student')),
                function (string $attribute, mixed $value, Closure $fail) use ($tenantId): void {
                    $belongsToTenant = User::whereKey($value)
                        ->whereHas('memberships', fn ($q) => $q->where('tenant_id', $tenantId))
                        ->exists();

                    if (! $belongsToTenant) {
                        $fail('The selected '.$attribute.' is invalid.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'status' => 'required|string|in:paid,pending,overdue',
            'payment_method' => 'nullable|string|in:cash,online',
            'transaction_id' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $payment = FeePayment::create(array_merge($validated, ['tenant_id' => $tenantId]));

        return response()->json($payment->load('student'), 201);
    }

    public function show(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $payment = FeePayment::where('tenant_id', $tenantId)->with('student')->findOrFail($id);

        return response()->json($payment);
    }

    public function update(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $payment = FeePayment::where('tenant_id', $tenantId)->findOrFail($id);
        $before = $payment->only([
            'id',
            'tenant_id',
            'user_id',
            'amount',
            'payment_date',
            'status',
            'payment_method',
            'transaction_id',
            'remarks',
        ]);

        $validated = $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'payment_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|in:paid,pending,overdue',
            'payment_method' => 'sometimes|nullable|string|in:cash,online',
            'transaction_id' => 'sometimes|nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $payment->update($validated);
        $payment->refresh();

        app(AuditLogService::class)->log(
            action: 'fee_payment.updated',
            entityType: FeePayment::class,
            entityId: $payment->id,
            oldValues: $before,
            newValues: $payment->only([
                'id',
                'tenant_id',
                'user_id',
                'amount',
                'payment_date',
                'status',
                'payment_method',
                'transaction_id',
                'remarks',
            ]),
            actor: $request->user(),
            tenantId: $tenantId,
            request: $request
        );

        return response()->json($payment->load('student'));
    }

    public function destroy(Request $request, string $id)
    {
        $tenantId = $request->user()->tenant_id;
        $payment = FeePayment::where('tenant_id', $tenantId)->findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment record deleted']);
    }
}
