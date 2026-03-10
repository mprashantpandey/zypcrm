<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Services\NotificationTemplateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.app')]
class Settings extends Component
{
    use WithFileUploads;

    public $activeTab = 'general'; // general, branding, seo, mail, auth, payment, firebase, notifications, third_party, templates, app_links, modules, embeds

    // --- General Settings ---
    public $siteTitle = 'LibrarySaaS';
    public $contactEmail = '';
    public $contactPhone = '';
    public $address = '';
    public $timezone = 'UTC';
    public $currency = 'USD';
    public $currencySymbol = '$';
    public $dateFormat = 'Y-m-d';
    public $timeFormat = 'H:i:s';
    public $leavePolicyMode = 'none';
    public $leavePolicyCapDaysPerMonth = 0;

    // --- Branding Settings ---
    public $appName = 'LibrarySaaS';
    public $primaryColor = '#4f46e5';
    public $secondaryColor = '#0ea5e9';
    public $accentColor = '#22c55e';
    public $themeMode = 'light'; // light, dark, auto

    // File Uploads
    public $lightLogo;
    public $darkLogo;
    public $favicon;
    public $existingLightLogo;
    public $existingDarkLogo;
    public $existingFavicon;

    // --- SEO Settings ---
    public $seoMetaKeywords = '';
    public $seoMetaDescription = '';
    public $googleAnalyticsId = '';

    // --- Auth Settings ---
    public $allowRegistration = true;
    public $requireEmailVerification = false;
    public $emailPasswordAuthEnabled = true;

    // --- SMTP / Email ---
    public $mailHost = '';
    public $mailPort = '587';
    public $mailUsername = '';
    public $mailPassword = '';
    public $mailFromAddress = '';

    // --- Payment Gateways / Billing ---
    public $enableStripe = false;
    public $stripeKey = '';
    public $stripeSecret = '';

    public $enableRazorpay = false;
    public $razorpayKey = '';
    public $razorpaySecret = '';

    public $enableManualPayment = true;
    public $manualPaymentInstructions = '';

    public $enableManualSubscriptionApproval = true;

    // Platform Fee Configuration
    public $enablePlatformFeeCollection = false;
    public $platformFeePercentage = 0;

    // --- Firebase / Push Notifications (Admin SDK / FCM HTTP v1) ---
    public $firebaseEnabled = false;
    public $firebaseServiceAccountJson = '';  // Full service account JSON from Firebase Console
    // Web / Flutter client config (from Firebase Console -> General -> Your Apps)
    public $firebaseApiKey = '';
    public $firebaseAuthDomain = '';
    public $firebaseAppId = '';
    public $firebaseVapidKey = '';

    // Firebase Phone Auth
    public $firebasePhoneAuthEnabled = false;
    public $firebaseTestPhoneNumbers = '';

    // --- App Links ---
    public $playStoreLink = '';
    public $appStoreLink = '';
    public $forceAppUpdate = false;
    public $currentAppVersion = '1.0.0';

    // --- Notification Channels ---
    public $notificationEmailEnabled = true;
    public $notificationPushEnabled = true;
    public $notificationWhatsappEnabled = false;
    public $notificationEventNoticeBroadcastEnabled = true;
    public $notificationEventLeaveStatusEnabled = true;
    public $notificationEventFeeDueReminderEnabled = true;
    public $notificationEventFeePaymentReceiptEnabled = true;
    public $notificationEventSubscriptionExpiryEnabled = true;

    // --- 3rd Party Configs ---
    public $whatsappProviderEnabled = false;
    public $whatsappProviderName = 'placeholder';
    public $whatsappApiBaseUrl = '';
    public $whatsappApiKey = '';
    public $whatsappSenderId = '';

    // --- Template Manager ---
    public $templateChannel = 'email';
    public $templateEventKey = 'notice_broadcast';
    public $templateName = '';
    public $templateSubject = '';
    public $templateBody = '';
    public $templateIsActive = true;
    public $testEmailTo = '';

    // --- Modules ---
    public $enableBlog = false;
    public $enableSupportTickets = false;

    // --- Embed Codes ---
    public $tawkToEmbedCode = '';
    public $analyticsCustomJs = '';

    // --- Scheduler Health ---
    public $schedulerLastRunAt = null;
    public $schedulerLastRunCommand = null;
    public $schedulerLastRunHuman = null;

    public function mount()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // General
        $this->siteTitle = $settings['site_title'] ?? 'LibrarySaaS';
        $this->contactEmail = $settings['contact_email'] ?? '';
        $this->contactPhone = $settings['contact_phone'] ?? '';
        $this->address = $settings['address'] ?? '';
        $this->timezone = $settings['timezone'] ?? 'UTC';
        $this->currency = $settings['currency'] ?? 'USD';
        $this->currencySymbol = $settings['currency_symbol'] ?? '$';
        $this->dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $this->timeFormat = $settings['time_format'] ?? 'H:i:s';
        $this->leavePolicyMode = $settings['leave_policy_mode'] ?? 'none';
        $this->leavePolicyCapDaysPerMonth = (int) ($settings['leave_policy_cap_days_per_month'] ?? 0);

        // Branding
        $this->appName = $settings['app_name'] ?? 'LibrarySaaS';
        $this->primaryColor = $settings['primary_color'] ?? '#4f46e5';
        $this->secondaryColor = $settings['secondary_color'] ?? '#0ea5e9';
        $this->accentColor = $settings['accent_color'] ?? '#22c55e';
        $this->themeMode = $settings['theme_mode'] ?? 'light';
        $this->existingLightLogo = $settings['light_logo'] ?? null;
        $this->existingDarkLogo = $settings['dark_logo'] ?? null;
        $this->existingFavicon = $settings['favicon'] ?? null;

        // SEO
        $this->seoMetaKeywords = $settings['seo_meta_keywords'] ?? '';
        $this->seoMetaDescription = $settings['seo_meta_description'] ?? '';
        $this->googleAnalyticsId = $settings['google_analytics_id'] ?? '';

        // Auth
        $this->allowRegistration = filter_var($settings['allow_registration'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->requireEmailVerification = filter_var($settings['require_email_verification'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->emailPasswordAuthEnabled = filter_var($settings['email_password_auth_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);

        // Mail
        $this->mailHost = $settings['mail_host'] ?? '';
        $this->mailPort = $settings['mail_port'] ?? '587';
        $this->mailUsername = $settings['mail_username'] ?? '';
        $this->mailPassword = $settings['mail_password'] ?? '';
        $this->mailFromAddress = $settings['mail_from_address'] ?? '';

        // Payment
        $this->enableStripe = filter_var($settings['enable_stripe'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->stripeKey = $settings['stripe_key'] ?? '';
        $this->stripeSecret = $settings['stripe_secret'] ?? '';

        $this->enableRazorpay = filter_var($settings['enable_razorpay'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->razorpayKey = $settings['razorpay_key'] ?? '';
        $this->razorpaySecret = $settings['razorpay_secret'] ?? '';

        $this->enableManualPayment = filter_var($settings['enable_manual_payment'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->manualPaymentInstructions = $settings['manual_payment_instructions'] ?? '';
        $this->enableManualSubscriptionApproval = filter_var($settings['enable_manual_subscription_approval'] ?? true, FILTER_VALIDATE_BOOLEAN);

        // Platform Fee Configuration
        $this->enablePlatformFeeCollection = filter_var($settings['enable_platform_fee_collection'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->platformFeePercentage = $settings['platform_fee_percentage'] ?? 0;

        // Firebase — Admin SDK
        $this->firebaseEnabled = filter_var($settings['firebase_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->firebaseServiceAccountJson = $settings['firebase_service_account_json'] ?? '';
        $this->firebaseApiKey = $settings['firebase_api_key'] ?? '';
        $this->firebaseAuthDomain = $settings['firebase_auth_domain'] ?? '';
        $this->firebaseAppId = $settings['firebase_app_id'] ?? '';
        $this->firebaseVapidKey = $settings['firebase_vapid_key'] ?? '';
        $this->firebasePhoneAuthEnabled = filter_var($settings['firebase_phone_auth_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->firebaseTestPhoneNumbers = $settings['firebase_test_phone_numbers'] ?? '';

        // App Links
        $this->playStoreLink = $settings['play_store_link'] ?? '';
        $this->appStoreLink = $settings['app_store_link'] ?? '';
        $this->forceAppUpdate = filter_var($settings['force_app_update'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->currentAppVersion = $settings['current_app_version'] ?? '1.0.0';

        // Notification Channels
        $this->notificationEmailEnabled = filter_var($settings['notification_email_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationPushEnabled = filter_var($settings['notification_push_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationWhatsappEnabled = filter_var($settings['notification_whatsapp_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->notificationEventNoticeBroadcastEnabled = filter_var($settings['notification_event_notice_broadcast_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationEventLeaveStatusEnabled = filter_var($settings['notification_event_leave_status_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationEventFeeDueReminderEnabled = filter_var($settings['notification_event_fee_due_reminder_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationEventFeePaymentReceiptEnabled = filter_var($settings['notification_event_fee_payment_receipt_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $this->notificationEventSubscriptionExpiryEnabled = filter_var($settings['notification_event_subscription_expiry_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);

        // 3rd Party Configs
        $this->whatsappProviderEnabled = filter_var($settings['whatsapp_provider_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->whatsappProviderName = $settings['whatsapp_provider_name'] ?? 'placeholder';
        $this->whatsappApiBaseUrl = $settings['whatsapp_api_base_url'] ?? '';
        $this->whatsappApiKey = $settings['whatsapp_api_key'] ?? '';
        $this->whatsappSenderId = $settings['whatsapp_sender_id'] ?? '';

        $this->testEmailTo = Auth::user()?->email ?? ($settings['contact_email'] ?? '');
        app(NotificationTemplateService::class)->seedDefaults();
        $this->loadTemplateEditor();

        // Modules
        $this->enableBlog = filter_var($settings['enable_blog'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->enableSupportTickets = filter_var($settings['enable_support_tickets'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Embed Codes
        $this->tawkToEmbedCode = $settings['tawkto_embed_code'] ?? '';
        $this->analyticsCustomJs = $settings['analytics_custom_js'] ?? '';

        // Scheduler Health
        $this->schedulerLastRunAt = $settings['scheduler_last_run_at'] ?? null;
        $this->schedulerLastRunCommand = $settings['scheduler_last_run_command'] ?? null;
        $this->schedulerLastRunHuman = $this->schedulerLastRunAt
            ? Carbon::parse($this->schedulerLastRunAt)->diffForHumans()
            : null;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveGeneral()
    {
        $this->validate([
            'leavePolicyMode' => 'required|in:none,full,capped',
            'leavePolicyCapDaysPerMonth' => 'nullable|integer|min:0|max:31',
        ]);

        $this->saveSetting('site_title', $this->siteTitle, 'general');
        $this->saveSetting('contact_email', $this->contactEmail, 'general');
        $this->saveSetting('contact_phone', $this->contactPhone, 'general');
        $this->saveSetting('address', $this->address, 'general');
        $this->saveSetting('timezone', $this->timezone, 'general');
        $this->saveSetting('currency', $this->currency, 'general');
        $this->saveSetting('currency_symbol', $this->currencySymbol, 'general');
        $this->saveSetting('date_format', $this->dateFormat, 'general');
        $this->saveSetting('time_format', $this->timeFormat, 'general');
        $this->saveSetting('leave_policy_mode', $this->leavePolicyMode, 'policy');
        $this->saveSetting(
            'leave_policy_cap_days_per_month',
            $this->leavePolicyMode === 'capped' ? (int) $this->leavePolicyCapDaysPerMonth : 0,
            'policy'
        );
        session()->flash('message', 'General settings saved successfully.');
    }

    public function saveBranding()
    {
        $this->validate([
            'primaryColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondaryColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'accentColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $this->saveSetting('app_name', $this->appName, 'branding');
        $this->saveSetting('primary_color', $this->primaryColor, 'branding');
        $this->saveSetting('secondary_color', $this->secondaryColor, 'branding');
        $this->saveSetting('accent_color', $this->accentColor, 'branding');
        $this->saveSetting('theme_mode', $this->themeMode, 'branding');

        if ($this->lightLogo) {
            $path = $this->lightLogo->store('branding', 'public');
            $this->saveSetting('light_logo', $path, 'branding');
            $this->existingLightLogo = $path;
        }

        if ($this->darkLogo) {
            $path = $this->darkLogo->store('branding', 'public');
            $this->saveSetting('dark_logo', $path, 'branding');
            $this->existingDarkLogo = $path;
        }

        if ($this->favicon) {
            $path = $this->favicon->store('branding', 'public');
            $this->saveSetting('favicon', $path, 'branding');
            $this->existingFavicon = $path;
        }

        session()->flash('message', 'Branding settings & logic saved successfully.');
    }

    public function saveSeo()
    {
        $this->saveSetting('seo_meta_keywords', $this->seoMetaKeywords, 'seo');
        $this->saveSetting('seo_meta_description', $this->seoMetaDescription, 'seo');
        $this->saveSetting('google_analytics_id', $this->googleAnalyticsId, 'seo');
        session()->flash('message', 'SEO configurations saved successfully.');
    }

    public function saveAuth()
    {
        $this->saveSetting('allow_registration', $this->allowRegistration, 'auth');
        $this->saveSetting('require_email_verification', $this->requireEmailVerification, 'auth');
        $this->saveSetting('email_password_auth_enabled', $this->emailPasswordAuthEnabled, 'auth');
        $this->saveSetting('firebase_phone_auth_enabled', $this->firebasePhoneAuthEnabled, 'auth');
        session()->flash('message', 'Authentication preferences saved successfully.');
    }

    public function saveMail()
    {
        $this->saveSetting('mail_host', $this->mailHost, 'mail');
        $this->saveSetting('mail_port', $this->mailPort, 'mail');
        $this->saveSetting('mail_username', $this->mailUsername, 'mail');
        $this->saveSetting('mail_password', $this->mailPassword, 'mail');
        $this->saveSetting('mail_from_address', $this->mailFromAddress, 'mail');
        session()->flash('message', 'SMTP settings saved successfully.');
    }

    public function saveBilling()
    {
        $this->saveSetting('enable_stripe', $this->enableStripe, 'billing');
        $this->saveSetting('stripe_key', $this->stripeKey, 'billing');
        $this->saveSetting('stripe_secret', $this->stripeSecret, 'billing');

        $this->saveSetting('enable_razorpay', $this->enableRazorpay, 'billing');
        $this->saveSetting('razorpay_key', $this->razorpayKey, 'billing');
        $this->saveSetting('razorpay_secret', $this->razorpaySecret, 'billing');

        $this->saveSetting('enable_manual_payment', $this->enableManualPayment, 'billing');
        $this->saveSetting('manual_payment_instructions', $this->manualPaymentInstructions, 'billing');
        $this->saveSetting('enable_manual_subscription_approval', $this->enableManualSubscriptionApproval, 'billing');

        $this->saveSetting('enable_platform_fee_collection', $this->enablePlatformFeeCollection, 'billing');
        $this->saveSetting('platform_fee_percentage', $this->platformFeePercentage, 'billing');
        
        session()->flash('message', 'Payment gateway configurations saved successfully.');
    }

    public function saveFirebase()
    {
        $this->validate([
            'firebaseServiceAccountJson' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $fail('Service Account JSON must be valid JSON.');
                        } elseif (empty($decoded['project_id']) || empty($decoded['private_key'])) {
                            $fail('Service Account JSON must contain project_id and private_key fields.');
                        }
                    }
                }
            ],
        ]);

        $this->saveSetting('firebase_enabled', $this->firebaseEnabled, 'firebase');
        $this->saveSetting('firebase_service_account_json', $this->firebaseServiceAccountJson, 'firebase');
        $this->saveSetting('firebase_api_key', $this->firebaseApiKey, 'firebase');
        $this->saveSetting('firebase_auth_domain', $this->firebaseAuthDomain, 'firebase');
        $this->saveSetting('firebase_app_id', $this->firebaseAppId, 'firebase');
        $this->saveSetting('firebase_vapid_key', $this->firebaseVapidKey, 'firebase');
        $this->saveSetting('firebase_phone_auth_enabled', $this->firebasePhoneAuthEnabled, 'firebase');
        $this->saveSetting('firebase_test_phone_numbers', $this->firebaseTestPhoneNumbers, 'firebase');
        session()->flash('message', 'Firebase Admin SDK configs saved successfully.');
    }

    public function saveAppLinks()
    {
        $this->saveSetting('play_store_link', $this->playStoreLink, 'app_links');
        $this->saveSetting('app_store_link', $this->appStoreLink, 'app_links');
        $this->saveSetting('force_app_update', $this->forceAppUpdate, 'app_links');
        $this->saveSetting('current_app_version', $this->currentAppVersion, 'app_links');
        session()->flash('message', 'App Links and Version details saved.');
    }

    public function saveNotifications()
    {
        $this->saveSetting('notification_email_enabled', $this->notificationEmailEnabled, 'notifications');
        $this->saveSetting('notification_push_enabled', $this->notificationPushEnabled, 'notifications');
        $this->saveSetting('notification_whatsapp_enabled', $this->notificationWhatsappEnabled, 'notifications');
        $this->saveSetting('notification_event_notice_broadcast_enabled', $this->notificationEventNoticeBroadcastEnabled, 'notifications');
        $this->saveSetting('notification_event_leave_status_enabled', $this->notificationEventLeaveStatusEnabled, 'notifications');
        $this->saveSetting('notification_event_fee_due_reminder_enabled', $this->notificationEventFeeDueReminderEnabled, 'notifications');
        $this->saveSetting('notification_event_fee_payment_receipt_enabled', $this->notificationEventFeePaymentReceiptEnabled, 'notifications');
        $this->saveSetting('notification_event_subscription_expiry_enabled', $this->notificationEventSubscriptionExpiryEnabled, 'notifications');

        session()->flash('message', 'Notification channel and event preferences saved successfully.');
    }

    public function saveThirdPartyConfigs()
    {
        $this->validate([
            'whatsappProviderName' => 'required|string|max:100',
            'whatsappApiBaseUrl' => 'nullable|url|max:500',
            'whatsappApiKey' => 'nullable|string|max:500',
            'whatsappSenderId' => 'nullable|string|max:100',
        ]);

        $this->saveSetting('whatsapp_provider_enabled', $this->whatsappProviderEnabled, 'third_party');
        $this->saveSetting('whatsapp_provider_name', $this->whatsappProviderName, 'third_party');
        $this->saveSetting('whatsapp_api_base_url', $this->whatsappApiBaseUrl, 'third_party');
        $this->saveSetting('whatsapp_api_key', $this->whatsappApiKey, 'third_party');
        $this->saveSetting('whatsapp_sender_id', $this->whatsappSenderId, 'third_party');

        session()->flash('message', '3rd party provider configurations saved successfully.');
    }

    public function updatedTemplateChannel(): void
    {
        $this->loadTemplateEditor();
    }

    public function updatedTemplateEventKey(): void
    {
        $this->loadTemplateEditor();
    }

    public function loadTemplateEditor(): void
    {
        $service = app(NotificationTemplateService::class);
        $template = $service->getTemplate($this->templateEventKey, $this->templateChannel);
        $defaults = $service->defaultTemplate($this->templateEventKey, $this->templateChannel);

        $this->templateName = $template?->name ?? $defaults['name'];
        $this->templateSubject = (string) ($template?->subject ?? ($defaults['subject'] ?? ''));
        $this->templateBody = (string) ($template?->body ?? $defaults['body']);
        $this->templateIsActive = $template?->is_active ?? true;
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'templateChannel' => 'required|in:email,sms,whatsapp',
            'templateEventKey' => 'required|string|max:80',
            'templateName' => 'required|string|max:255',
            'templateSubject' => 'nullable|string|max:255',
            'templateBody' => 'required|string',
        ]);

        app(NotificationTemplateService::class)->upsertTemplate(
            $this->templateEventKey,
            $this->templateChannel,
            [
                'name' => $this->templateName,
                'subject' => $this->templateSubject,
                'body' => $this->templateBody,
                'is_active' => $this->templateIsActive,
            ]
        );

        session()->flash('message', 'Template saved successfully.');
    }

    public function sendTestMail(): void
    {
        $this->validate([
            'testEmailTo' => 'required|email',
        ]);

        if (! Setting::getBool('notification_email_enabled', true)) {
            $this->addError('testEmailTo', 'Email notifications are globally disabled.');

            return;
        }

        if ($this->templateChannel !== 'email') {
            $this->addError('testEmailTo', 'Switch to Email template channel before sending test email.');

            return;
        }

        $vars = $this->sampleTemplateVariables($this->templateEventKey);
        $rendered = app(NotificationTemplateService::class)->render($this->templateEventKey, 'email', $vars);
        $subject = trim((string) ($rendered['subject'] ?? 'Test Notification'));
        $html = (string) ($rendered['body'] ?? '');

        Mail::send('emails.dynamic-template', ['html' => $html], function ($message) use ($subject) {
            $message->to($this->testEmailTo)->subject($subject !== '' ? $subject : 'Test Notification');
        });

        session()->flash('message', 'Test email sent successfully.');
    }

    public function getTemplateVariablesProperty(): array
    {
        return app(NotificationTemplateService::class)->getVariableMap($this->templateEventKey);
    }

    public function getTemplateEventsProperty(): array
    {
        return NotificationTemplateService::EVENTS;
    }

    public function getTemplatePreviewProperty(): array
    {
        $vars = $this->sampleTemplateVariables($this->templateEventKey);

        $subject = $this->templateSubject;
        $body = $this->templateBody;

        foreach ($vars as $key => $value) {
            $token = '{{'.$key.'}}';
            $subject = str_replace($token, (string) $value, (string) $subject);
            $body = str_replace($token, (string) $value, (string) $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
            'variables' => $vars,
        ];
    }

    private function sampleTemplateVariables(string $eventKey): array
    {
        $base = [
            'app_name' => Setting::getValue('app_name', 'ZypCRM'),
            'site_title' => Setting::getValue('site_title', 'ZypCRM'),
            'user_name' => Auth::user()?->name ?? 'Test User',
            'tenant_name' => 'Demo Library',
            'dashboard_url' => url('/dashboard'),
            'date' => now()->format('M d, Y'),
            'level' => 'info',
            'notice_title' => 'Sample Notice',
            'notice_body' => 'This is a sample notice body.',
            'status' => 'approved',
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
            'extended_days' => '2',
            'updated_end_date' => now()->addDays(2)->toDateString(),
            'amount' => Setting::getCurrencySymbol('$').'999.00',
            'due_date' => now()->addDays(3)->format('M d, Y'),
            'pay_url' => url('/pay/demo-slug'),
            'payment_date' => now()->format('M d, Y'),
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'plan_name' => 'Gold Plan',
            'expiry_date' => now()->addDays(7)->format('M d, Y'),
        ];

        $allowed = app(NotificationTemplateService::class)->getVariableMap($eventKey);

        return collect($base)
            ->filter(fn ($_, $key) => in_array($key, $allowed, true))
            ->all();
    }

    public function saveModules()
    {
        $this->saveSetting('enable_blog', $this->enableBlog, 'modules');
        $this->saveSetting('enable_support_tickets', $this->enableSupportTickets, 'modules');
        session()->flash('message', 'Platform Module toggles updated.');
    }

    public function saveEmbeds()
    {
        $this->saveSetting('tawkto_embed_code', $this->tawkToEmbedCode, 'embeds');
        $this->saveSetting('analytics_custom_js', $this->analyticsCustomJs, 'embeds');
        session()->flash('message', 'Embed codes updated successfully.');
    }

    private function saveSetting($key, $value, $group = 'general')
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? 'true' : 'false') : $value, 'group' => $group]
        );
    }
    public function render()
    {
        return view('livewire.admin.settings');
    }
}
