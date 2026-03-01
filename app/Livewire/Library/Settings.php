<?php

namespace App\Livewire\Library;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public $activeTab = 'profile'; // profile, hours

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public $name;
    public $email;
    public $phone;
    public $address;
    
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
        
        if ($tenant->operating_hours && is_array($tenant->operating_hours)) {
            $this->operating_hours = array_merge($this->operating_hours, $tenant->operating_hours);
        }
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $tenant = Auth::user()->tenant;
        $tenant->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Library profile updated successfully.');
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

    public function render()
    {
        return view('livewire.library.settings')->layout('layouts.app', [
            'header' => 'Library Settings'
        ]);
    }
}