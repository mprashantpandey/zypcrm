<?php

namespace App\Livewire\Library;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Livewire\Component;

class StudentCard extends Component
{
    public $studentId;

    public function mount($id)
    {
        $this->studentId = $id;
    }

    public function render()
    {
        // Ensure student belongs to current tenant
        $student = User::with(['assignedSeat', 'activeSubscription.plan'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('role', 'student')
            ->findOrFail($this->studentId);

        // Generate a QR code linking to their web profile or carrying auth payload
        $qrCode = QrCode::size(120)
            ->style('round')
            ->margin(1)
            ->generate(route('login', ['email' => $student->email]));

        return view('livewire.library.student-card', compact('student', 'qrCode'))->layout('layouts.app', [
            'header' => 'Student ID Card'
        ]);
    }
}