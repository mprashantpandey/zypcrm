<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/pricing', function () {
    // Dynamic pricing from DB
    $plans = App\Models\SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
    return view('pricing', compact('plans'));
})->name('public.pricing');

Route::view('/about', 'about')->name('public.about');
Route::view('/contact', 'contact')->name('public.contact');
Route::view('/policies', 'policies')->name('public.policies');
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->role === 'library_owner') {
        return redirect()->route('library.dashboard');
    }
    if (auth()->user()->role === 'student') {
        return redirect()->route('student.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

use App\Livewire\Admin\SubscriptionPlans;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Tenants;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Students as AdminStudents;

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/subscription-plans', SubscriptionPlans::class)->name('plans');
    Route::get('/tenants', Tenants::class)->name('tenants');
    Route::get('/students', AdminStudents::class)->name('students');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
    Route::get('/support', \App\Livewire\Admin\SupportTickets::class)->name('support');
});

// Library Owner Routes
use App\Livewire\Library\Dashboard as LibraryDashboard;
use App\Livewire\Library\Students as LibraryStudents;
use App\Livewire\Library\Seats as LibrarySeats;
use App\Livewire\Library\Fees as LibraryFees;
use App\Livewire\Library\Settings as LibrarySettings;
use App\Livewire\Library\Attendance;

use App\Livewire\Library\Plans as LibraryPlans;
use App\Livewire\Student\Attendance as StudentAttendancePanel;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\Fees as StudentFeesPanel;
use App\Livewire\Student\Leaves as StudentLeavesPanel;

Route::get('/pay/{slug}', \App\Livewire\Public\FeePayment::class)->name('public.pay');

Route::middleware(['auth', 'verified', 'role:library_owner'])->prefix('library')->name('library.')->group(function () {
    Route::get('/dashboard', LibraryDashboard::class)->name('dashboard');
    Route::get('/plans', LibraryPlans::class)->name('plans');
    Route::get('/students', LibraryStudents::class)->name('students');
    Route::get('/seats', LibrarySeats::class)->name('seats');
    Route::get('/fees', LibraryFees::class)->name('fees');
    Route::get('/settings', LibrarySettings::class)->name('settings');
    // Route::get('/support', \App\Livewire\Library\Support::class)->name('support'); // TODO: Create Support component
    Route::get('/attendance', \App\Livewire\Library\Attendance::class)->name('attendance');
    Route::get('/leaves', \App\Livewire\Library\Leaves::class)->name('leaves');
    Route::get('/student/{id}/card', \App\Livewire\Library\StudentCard::class)->name('student.card');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/attendance', StudentAttendancePanel::class)->name('attendance');
    Route::get('/fees', StudentFeesPanel::class)->name('fees');
    Route::get('/leaves', StudentLeavesPanel::class)->name('leaves');
});

// Subscription Payment Routes
use App\Http\Controllers\SubscriptionController;

Route::middleware(['auth', 'verified'])->prefix('subscription')->name('subscription.')->group(function () {
    // Stripe
    Route::get('/stripe/checkout/{plan}', [SubscriptionController::class , 'stripeCheckout'])->name('stripe.checkout');
    Route::get('/stripe/success/{plan}', [SubscriptionController::class , 'stripeSuccess'])->name('stripe.success');
    Route::get('/stripe/cancel', [SubscriptionController::class , 'stripeCancel'])->name('stripe.cancel');
    // Razorpay
    Route::get('/razorpay/checkout/{plan}', [SubscriptionController::class , 'razorpayCheckout'])->name('razorpay.checkout');
    Route::post('/razorpay/verify/{plan}', [SubscriptionController::class , 'razorpayVerify'])->name('razorpay.verify');
});
