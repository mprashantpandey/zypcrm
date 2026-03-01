<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SubscriptionPlan;
use Livewire\Attributes\Layout;

class SubscriptionPlans extends Component
{
    public $name, $description, $price, $billing_cycle = 'monthly', $max_students = 0, $is_active = true, $plan_id;
    public $stripe_price_id, $razorpay_plan_id, $features;
    public $isModalOpen = false;

    #[Layout('layouts.app')]
    public function render()
    {
        $plans = SubscriptionPlan::all();
        return view('livewire.admin.subscription-plans', compact('plans'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->billing_cycle = 'monthly';
        $this->max_students = 0;
        $this->stripe_price_id = '';
        $this->razorpay_plan_id = '';
        $this->features = '';
        $this->is_active = true;
        $this->plan_id = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'billing_cycle' => 'required',
            'max_students' => 'required|numeric',
        ]);

        SubscriptionPlan::updateOrCreate(['id' => $this->plan_id], [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'max_students' => $this->max_students,
            'stripe_price_id' => $this->stripe_price_id,
            'razorpay_plan_id' => $this->razorpay_plan_id,
            'features' => $this->features ? array_map('trim', explode("\n", trim($this->features))) : [],
            'is_active' => $this->is_active
        ]);

        session()->flash('message', $this->plan_id ? 'Plan Updated Successfully.' : 'Plan Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $this->plan_id = $id;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->billing_cycle = $plan->billing_cycle;
        $this->max_students = $plan->max_students;
        $this->stripe_price_id = $plan->stripe_price_id;
        $this->razorpay_plan_id = $plan->razorpay_plan_id;
        $this->features = is_array($plan->features) ? implode("\n", $plan->features) : '';
        $this->is_active = $plan->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        SubscriptionPlan::find($id)->delete();
        session()->flash('message', 'Plan Deleted Successfully.');
    }
}