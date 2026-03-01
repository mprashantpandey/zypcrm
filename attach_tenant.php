<?php
try {
    $tenant = App\Models\Tenant::first();
    if (!$tenant) {
        $tenant = App\Models\Tenant::create(['name' => 'Test Tenant', 'domain' => 'zyptos.test', 'status' => 'active']);
    }
    $owner = App\Models\User::where('email', 'tenant@zyptos.test')->first();
    if ($owner) {
        $owner->tenant_id = $tenant->id;
        $owner->save();
        echo "Tenant {$tenant->id} assigned to owner {$owner->email}\n";
    } else {
        echo "Owner not found\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
