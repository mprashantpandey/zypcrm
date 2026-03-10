<?php

namespace App\Livewire\Library;

use App\Models\LibraryPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Plans extends Component
{
    public $name;
    public $price = 0;
    public $duration_days = 30;
    public $start_time;
    public $end_time;
    public $is_active = true;

    public $isEditing = false;
    public $editingPlanId = null;
    public $showPlanModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'price' => 'numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i|after:start_time',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'price', 'duration_days', 'start_time', 'end_time', 'is_active', 'isEditing', 'editingPlanId']);
        $this->isEditing = false;
        $this->showPlanModal = true;
    }

    public function editPlan($id)
    {
        $this->resetValidation();
        $plan = LibraryPlan::findOrFail($id);

        $this->isEditing = true;
        $this->editingPlanId = $plan->id;

        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->duration_days = $plan->duration_days;
        $this->start_time = $plan->start_time ? clone $plan->start_time : null; // Handles time properly if it evaluates to object

        // Ensure string format for inputs
        if ($plan->start_time) {
            $this->start_time = \Carbon\Carbon::parse($plan->start_time)->format('H:i');
        }
        if ($plan->end_time) {
            $this->end_time = \Carbon\Carbon::parse($plan->end_time)->format('H:i');
        }

        $this->is_active = $plan->is_active;

        $this->showPlanModal = true;
    }

    public function savePlan()
    {
        $validatedData = $this->validate();
        if (! $this->validatePlanAgainstOperatingHours()) {
            return;
        }

        if ($this->isEditing) {
            $plan = LibraryPlan::findOrFail($this->editingPlanId);
            $plan->update($validatedData);
            session()->flash('message', 'Library Plan updated successfully.');
        }
        else {
            LibraryPlan::create($validatedData);
            session()->flash('message', 'Library Plan created successfully.');
        }

        $this->showPlanModal = false;
        $this->reset(['name', 'price', 'duration_days', 'start_time', 'end_time', 'is_active', 'isEditing', 'editingPlanId']);
    }

    public function deletePlan($id)
    {
        $plan = LibraryPlan::findOrFail($id);

        // Prevent deletion if subscriptions exist (we haven't built this guard fully yet, but good practice to check)
        if ($plan->subscriptions()->count() > 0) { // Will need a relation in LibraryPlan model
            session()->flash('error', 'Cannot delete plan as students are subscribed to it. Deactivate it instead.');
            return;
        }

        $plan->delete();
        session()->flash('message', 'Plan deleted successfully.');
    }

    private function validatePlanAgainstOperatingHours(): bool
    {
        $tenant = Auth::user()?->tenant;
        $hours = $tenant?->operating_hours;

        if (! is_array($hours) || empty($hours)) {
            $this->addError('start_time', 'Library operating hours are missing. Configure them in Library Settings before creating/editing plans.');

            return false;
        }

        $planStart = $this->timeToMinutes($this->start_time ?: '00:00');
        $planEnd = $this->timeToMinutes($this->end_time ?: '23:59');

        if ($planStart === null || $planEnd === null || $planEnd <= $planStart) {
            $this->addError('start_time', 'Invalid plan timing. Ensure start/end times are valid and end time is after start time.');

            return false;
        }

        $openDays = 0;
        $invalidDays = [];
        $mismatchDays = [];

        foreach ($hours as $day => $window) {
            $isClosed = filter_var($window['closed'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($isClosed) {
                continue;
            }

            $openDays++;
            $openRaw = $window['open'] ?? null;
            $closeRaw = $window['close'] ?? null;
            $openMinutes = $this->timeToMinutes($openRaw);
            $closeMinutes = $this->timeToMinutes($closeRaw);

            $dayLabel = ucfirst((string) $day);
            if ($openMinutes === null || $closeMinutes === null || $closeMinutes <= $openMinutes) {
                $invalidDays[] = $dayLabel;
                continue;
            }

            if ($planStart < $openMinutes || $planEnd > $closeMinutes) {
                $mismatchDays[] = "{$dayLabel} ({$this->formatMinutes($openMinutes)}-{$this->formatMinutes($closeMinutes)})";
            }
        }

        if ($openDays === 0) {
            $this->addError('start_time', 'All operating days are marked closed. Set at least one open day in Library Settings before creating/editing plans.');

            return false;
        }

        if (! empty($invalidDays)) {
            $this->addError('start_time', 'Operating hours are invalid for: '.implode(', ', $invalidDays).'. Fix those day windows in Library Settings.');

            return false;
        }

        if (! empty($mismatchDays)) {
            $planWindow = $this->formatMinutes($planStart).'-'.$this->formatMinutes($planEnd);
            $this->addError(
                'start_time',
                'Plan timing '.$planWindow.' is outside library operating hours on: '.implode(', ', $mismatchDays).'.'
            );

            return false;
        }

        return true;
    }

    private function timeToMinutes(?string $value): ?int
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (! preg_match('/^(?<h>\d{2}):(?<m>\d{2})/', trim($value), $parts)) {
            return null;
        }

        $h = (int) $parts['h'];
        $m = (int) $parts['m'];
        if ($h < 0 || $h > 23 || $m < 0 || $m > 59) {
            return null;
        }

        return ($h * 60) + $m;
    }

    private function formatMinutes(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return sprintf('%02d:%02d', $h, $m);
    }

    public function render()
    {
        return view('livewire.library.plans', [
            'plans' => LibraryPlan::latest()->get()
        ]);
    }
}
