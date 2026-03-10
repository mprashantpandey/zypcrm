<?php

namespace App\Livewire\Library;

use App\Models\TenantImage;
use App\Services\UploadSecurityService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $activeTab = 'profile'; // profile, hours, attendance_security

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public $name;
    public $email;
    public $phone;
    public $address;
    public $public_slug;
    public $public_description;
    public bool $public_page_enabled = true;
    public array $publicImages = [];

    // Attendance security
    public bool $attendance_enforce_ip = false;
    public string $attendance_allowed_ips = '';
    public bool $attendance_enforce_device = false;
    public bool $attendance_geofence_enabled = false;
    public ?float $attendance_geofence_latitude = null;
    public ?float $attendance_geofence_longitude = null;
    public int $attendance_geofence_radius_meters = 150;
    
    // Operating Hours
    public $operating_hours = [
        'monday' => ['open' => '08:00', 'close' => '20:00', 'closed' => false],
        'tuesday' => ['open' => '08:00', 'close' => '20:00', 'closed' => false],
        'wednesday' => ['open' => '08:00', 'close' => '20:00', 'closed' => false],
        'thursday' => ['open' => '08:00', 'close' => '20:00', 'closed' => false],
        'friday' => ['open' => '08:00', 'close' => '20:00', 'closed' => false],
        'saturday' => ['open' => '09:00', 'close' => '18:00', 'closed' => false],
        'sunday' => ['open' => '09:00', 'close' => '18:00', 'closed' => true],
    ];

    public array $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function mount()
    {
        $tenant = Auth::user()->tenant;
        
        $this->name = $tenant->name;
        $this->email = $tenant->email;
        $this->phone = $tenant->phone;
        $this->address = $tenant->address;
        if (Schema::hasColumn('tenants', 'public_slug')) {
            $this->public_slug = $tenant->public_slug ?: Str::slug((string) $tenant->name);
            $this->public_description = $tenant->public_description;
            $this->public_page_enabled = (bool) ($tenant->public_page_enabled ?? true);
        } else {
            $this->public_slug = Str::slug((string) $tenant->name);
            $this->public_description = null;
            $this->public_page_enabled = true;
        }
        
        if ($tenant->operating_hours && is_array($tenant->operating_hours)) {
            $this->operating_hours = array_merge($this->operating_hours, $tenant->operating_hours);
        }

        $security = $tenant->attendance_security_settings ?? [];
        if (is_array($security)) {
            $this->attendance_enforce_ip = (bool) ($security['enforce_ip'] ?? false);
            $this->attendance_allowed_ips = (string) ($security['allowed_ips'] ?? '');
            $this->attendance_enforce_device = (bool) ($security['enforce_device'] ?? false);
            $this->attendance_geofence_enabled = (bool) ($security['geofence_enabled'] ?? false);
            $this->attendance_geofence_latitude = isset($security['geofence_latitude']) ? (float) $security['geofence_latitude'] : null;
            $this->attendance_geofence_longitude = isset($security['geofence_longitude']) ? (float) $security['geofence_longitude'] : null;
            $this->attendance_geofence_radius_meters = (int) ($security['geofence_radius_meters'] ?? 150);
        }
    }

    public function saveProfile()
    {
        $tenant = Auth::user()->tenant;
        $normalizedSlug = Str::slug((string) $this->public_slug);
        if ($normalizedSlug === '') {
            $normalizedSlug = Str::slug((string) $this->name);
        }
        if ($normalizedSlug === '') {
            $normalizedSlug = 'library-'.$tenant->id;
        }
        $this->public_slug = $normalizedSlug;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
        if (Schema::hasColumn('tenants', 'public_slug')) {
            $rules['public_slug'] = 'required|string|max:120|alpha_dash|unique:tenants,public_slug,'.$tenant->id;
            $rules['public_description'] = 'nullable|string|max:1200';
            $rules['public_page_enabled'] = 'boolean';
        }
        $this->validate($rules);

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ];
        if (Schema::hasColumn('tenants', 'public_slug')) {
            $payload['public_slug'] = $this->public_slug;
            $payload['public_description'] = $this->public_description;
            $payload['public_page_enabled'] = (bool) $this->public_page_enabled;
        }
        $tenant->update($payload);

        $this->dispatch('notify', type: 'success', message: 'Library profile updated successfully.');
    }

    public function getPublicPageUrlProperty(): string
    {
        if (empty($this->public_slug)) {
            return '';
        }

        return route('public.library', ['slug' => $this->public_slug]);
    }

    public function saveOperatingHours()
    {
        // Simple validation to ensure structured data
        $this->validate([
            'operating_hours.*.open' => 'required_unless:operating_hours.*.closed,true',
            'operating_hours.*.close' => 'required_unless:operating_hours.*.closed,true',
            'operating_hours.*.closed' => 'boolean',
        ]);

        $tenant = Auth::user()->tenant;
        $tenant->update([
            'operating_hours' => $this->operating_hours,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Operating hours updated successfully.');
    }

    public function saveAttendanceSecurity(): void
    {
        if (! Schema::hasColumn('tenants', 'attendance_security_settings')) {
            $this->dispatch('notify', type: 'error', message: 'Attendance security requires latest migrations.');

            return;
        }

        $this->validate([
            'attendance_allowed_ips' => 'nullable|string|max:1500',
            'attendance_geofence_latitude' => 'nullable|numeric|between:-90,90',
            'attendance_geofence_longitude' => 'nullable|numeric|between:-180,180',
            'attendance_geofence_radius_meters' => 'required|integer|min:30|max:5000',
        ]);

        if ($this->attendance_enforce_ip && trim($this->attendance_allowed_ips) === '') {
            $this->addError('attendance_allowed_ips', 'Allowed IP list is required when IP enforcement is enabled.');

            return;
        }

        if ($this->attendance_geofence_enabled && ($this->attendance_geofence_latitude === null || $this->attendance_geofence_longitude === null)) {
            $this->addError('attendance_geofence_latitude', 'Latitude and longitude are required when geofence is enabled.');

            return;
        }

        Auth::user()->tenant->update([
            'attendance_security_settings' => [
                'enforce_ip' => (bool) $this->attendance_enforce_ip,
                'allowed_ips' => trim((string) $this->attendance_allowed_ips),
                'enforce_device' => (bool) $this->attendance_enforce_device,
                'geofence_enabled' => (bool) $this->attendance_geofence_enabled,
                'geofence_latitude' => $this->attendance_geofence_latitude,
                'geofence_longitude' => $this->attendance_geofence_longitude,
                'geofence_radius_meters' => (int) $this->attendance_geofence_radius_meters,
            ],
        ]);

        $this->dispatch('notify', type: 'success', message: 'Attendance security settings updated.');
    }

    public function uploadPublicImages(): void
    {
        if (! Schema::hasTable('tenant_images')) {
            $this->dispatch('notify', type: 'error', message: 'Image gallery is not available yet. Run migrations first.');

            return;
        }

        $this->validate([
            'publicImages' => ['required', 'array', 'min:1', 'max:10'],
            'publicImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $tenantId = Auth::user()->tenant_id;
        $nextSortOrder = (int) TenantImage::where('tenant_id', $tenantId)->max('sort_order') + 1;
        $scanner = app(UploadSecurityService::class);

        foreach ($this->publicImages as $index => $image) {
            [$valid, $message] = $scanner->validateImageUpload($image);
            if (! $valid) {
                $this->addError("publicImages.{$index}", $message ?: 'Image failed security validation.');

                return;
            }
        }

        foreach ($this->publicImages as $image) {
            $path = $image->store('tenant-gallery', 'public');
            TenantImage::create([
                'tenant_id' => $tenantId,
                'image_path' => $path,
                'sort_order' => $nextSortOrder++,
                'is_active' => true,
            ]);
        }

        $this->publicImages = [];
        $this->dispatch('notify', type: 'success', message: 'Gallery images uploaded.');
    }

    public function deletePublicImage(int $imageId): void
    {
        if (! Schema::hasTable('tenant_images')) {
            return;
        }

        $image = TenantImage::query()
            ->where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($imageId);

        if (! empty($image->image_path) && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();
        $this->dispatch('notify', type: 'success', message: 'Image removed from gallery.');
    }

    public function render()
    {
        $galleryImages = collect();
        if (Schema::hasTable('tenant_images')) {
            $galleryImages = TenantImage::query()
                ->where('tenant_id', Auth::user()->tenant_id)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get();
        }

        return view('livewire.library.settings', [
            'galleryImages' => $galleryImages,
        ])->layout('layouts.app', [
            'header' => 'Library Settings'
        ]);
    }
}
