<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicLibraryController;
use App\Http\Controllers\Admin\TenantSubscriptionInvoiceController;
use App\Http\Controllers\Admin\DisasterReadinessController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\BlogController;
use App\Models\Setting;

Route::view('/', 'welcome')->name('public.home');
Route::get('/pricing', function () {
    $plans = App\Models\SubscriptionPlan::where('is_active', true)->orderBy('price')->get();
    return view('pricing', compact('plans'));
})->name('public.pricing');
Route::view('/about', 'about')->name('public.about');
Route::view('/contact', 'contact')->name('public.contact');
Route::view('/policies', 'policies')->name('public.policies');
Route::redirect('/terms', '/policies')->name('public.terms');
Route::redirect('/privacy', '/policies')->name('public.privacy');
Route::get('/health', HealthCheckController::class)->name('health');

if (Setting::getBool('enable_blog', false)) {
    Route::get('/blog', [BlogController::class, 'index'])->name('public.blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('public.blog.show');
}
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

// Web callback used after Firebase Phone OTP login in the browser.
// POST with JSON { "token": "..." } preferred (token not in URL); GET ?token=... still supported.
Route::match(['get', 'post'], '/login/phone/callback', \App\Http\Controllers\Auth\PhoneOtpWebLoginController::class)
    ->name('login.phone.callback');

use App\Livewire\Admin\SubscriptionPlans;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Tenants;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\Students as AdminStudents;
use App\Livewire\Admin\Notices as AdminNotices;
use App\Livewire\Admin\OpsMonitor as AdminOpsMonitor;
use App\Livewire\Admin\TenantProfile as AdminTenantProfile;
use App\Livewire\Admin\ExportCenter as AdminExportCenter;
use App\Livewire\Admin\StudentProfile as AdminStudentProfile;
use App\Livewire\Admin\AttendanceRiskMonitor as AdminAttendanceRiskMonitor;
use App\Livewire\Admin\CampaignManager as AdminCampaignManager;
use App\Livewire\Admin\DisasterReadiness as AdminDisasterReadiness;
use App\Livewire\Admin\BlogPosts as AdminBlogPosts;

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/subscription-plans', SubscriptionPlans::class)->name('plans');
    Route::get('/tenants', Tenants::class)->name('tenants');
    Route::get('/tenants/{tenant}', AdminTenantProfile::class)->name('tenants.show');
    Route::get('/tenants/{tenant}/invoices/{invoice}/pdf', [TenantSubscriptionInvoiceController::class, 'downloadPdf'])->name('tenants.invoices.pdf');
    Route::post('/tenants/{tenant}/invoices/{invoice}/email', [TenantSubscriptionInvoiceController::class, 'sendReceipt'])->name('tenants.invoices.email');
    Route::get('/students', AdminStudents::class)->name('students');
    Route::get('/students/{student}', AdminStudentProfile::class)->name('students.show');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/campaigns', AdminCampaignManager::class)->name('campaigns');
    Route::get('/exports', AdminExportCenter::class)->name('exports');
    Route::get('/attendance-risk', AdminAttendanceRiskMonitor::class)->name('attendance-risk');
    Route::get('/ops-monitor', AdminOpsMonitor::class)->name('ops-monitor');
    Route::get('/disaster-readiness', AdminDisasterReadiness::class)->name('disaster-readiness');
    Route::get('/disaster-readiness/snapshots/{snapshot}/download', [DisasterReadinessController::class, 'downloadSnapshot'])->name('disaster.snapshot.download');
    Route::get('/notices', AdminNotices::class)->name('notices');
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');

    if (Setting::getBool('enable_support_tickets', false)) {
        Route::get('/support', \App\Livewire\Admin\SupportTickets::class)->name('support');
    }

    if (Setting::getBool('enable_blog', false)) {
        Route::get('/blog', AdminBlogPosts::class)->name('blog');
    }
});

// Library Owner Routes
use App\Livewire\Library\Dashboard as LibraryDashboard;
use App\Livewire\Library\Students as LibraryStudents;
use App\Livewire\Library\Seats as LibrarySeats;
use App\Livewire\Library\Fees as LibraryFees;
use App\Livewire\Library\Settings as LibrarySettings;
use App\Livewire\Library\Attendance;
use App\Livewire\Library\AttendanceView;

use App\Livewire\Library\Plans as LibraryPlans;
use App\Livewire\Student\Attendance as StudentAttendancePanel;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\Fees as StudentFeesPanel;
use App\Livewire\Student\Leaves as StudentLeavesPanel;
use App\Livewire\Student\Notices as StudentNoticesPanel;
use App\Livewire\Library\Notices as LibraryNotices;
use App\Livewire\Library\ExportCenter as LibraryExportCenter;
use App\Livewire\Library\Support as LibrarySupport;

Route::get('/pay/{slug}', \App\Livewire\Public\FeePayment::class)->name('public.pay');
Route::get('/libraries/{slug}', [PublicLibraryController::class, 'show'])->name('public.library');
Route::post('/libraries/{slug}/contact', [PublicLibraryController::class, 'submitLead'])
    ->middleware('throttle:public-library-contact')
    ->name('public.library.contact');

Route::middleware(['auth', 'verified', 'role:library_owner'])->prefix('library')->name('library.')->group(function () {
    Route::get('/dashboard', LibraryDashboard::class)->name('dashboard');
    Route::get('/plans', LibraryPlans::class)->name('plans');
    Route::get('/students', LibraryStudents::class)->name('students');
    Route::get('/seats', LibrarySeats::class)->name('seats');
    Route::get('/fees', LibraryFees::class)->name('fees');
    Route::get('/leads', \App\Livewire\Library\Leads::class)->name('leads');
    Route::get('/exports', LibraryExportCenter::class)->name('exports');
    Route::get('/settings', LibrarySettings::class)->name('settings');
    if (Setting::getBool('enable_support_tickets', false)) {
        Route::get('/support', LibrarySupport::class)->name('support');
    }
    Route::get('/attendance', AttendanceView::class)->name('attendance');
    Route::get('/attendance/mark', Attendance::class)->name('attendance.mark');
    Route::get('/leaves', \App\Livewire\Library\Leaves::class)->name('leaves');
    Route::get('/notices', LibraryNotices::class)->name('notices');
    Route::get('/student/{id}/card', \App\Livewire\Library\StudentCard::class)->name('student.card');
});

Route::middleware(['auth', 'verified', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/attendance', StudentAttendancePanel::class)->name('attendance');
    Route::get('/fees', StudentFeesPanel::class)->name('fees');
    Route::get('/leaves', StudentLeavesPanel::class)->name('leaves');
    Route::get('/notices', StudentNoticesPanel::class)->name('notices');
    if (Setting::getBool('enable_support_tickets', false)) {
        Route::get('/support', \App\Livewire\Student\Support::class)->name('support');
    }
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
