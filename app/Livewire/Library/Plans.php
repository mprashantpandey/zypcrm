<?php

namespace App\Livewire\Library;

use App\Models\LibraryPlan;
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

    public function render()
    {
        return view('livewire.library.plans', [
            'plans' => LibraryPlan::latest()->get()
        ]);
    }
}