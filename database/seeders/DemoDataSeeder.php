<?php

namespace Database\Seeders;

use App\Models\FeePayment;
use App\Models\LibraryPlan;
use App\Models\Notice;
use App\Models\Seat;
use App\Models\Setting;
use App\Models\StudentAttendance;
use App\Models\StudentLeave;
use App\Models\StudentMembership;
use App\Models\StudentSubscription;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->truncateData();
        $this->seedSettings();

        $platformPlans = $this->seedPlatformPlans();
        $superAdmin = $this->seedSuperAdmin();
        [$tenantA, $tenantB] = $this->seedTenants();
        [$ownerA, $ownerB] = $this->seedOwners($tenantA, $tenantB);
        [$studentA1, $studentA2, $studentB1, $studentDual] = $this->seedStudents($tenantA, $tenantB);

        $this->seedMemberships($tenantA, $tenantB, $studentA1, $studentA2, $studentB1, $studentDual);
        [$seatA1, $seatA2, $seatB1] = $this->seedSeats($tenantA, $tenantB, $studentA1, $studentA2, $studentB1);
        [$planA, $planB] = $this->seedLibraryPlans($tenantA, $tenantB);

        $this->seedTenantSubscriptions($tenantA, $tenantB, $platformPlans);
        $this->seedStudentSubscriptions($tenantA, $tenantB, $planA, $planB, $studentA1, $studentA2, $studentB1, $studentDual, $seatA1, $seatA2, $seatB1);
        $this->seedFeePayments($tenantA, $tenantB, $studentA1, $studentA2, $studentB1, $studentDual);
        $this->seedAttendance($tenantA, $tenantB, $studentA1, $studentA2, $studentB1, $studentDual);
        $this->seedLeaves($tenantA, $tenantB, $studentA1, $studentB1);
        $this->seedSupportTickets($ownerA, $ownerB, $superAdmin);
        $this->seedNotices($superAdmin, $ownerA, $tenantA);
    }

    private function truncateData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'support_ticket_replies',
            'support_tickets',
            'student_attendances',
            'student_leaves',
            'student_subscriptions',
            'student_memberships',
            'fee_payments',
            'seats',
            'subscriptions',
            'library_plans',
            'subscription_plans',
            'audit_logs',
            'notices',
            'notifications',
            'notification_templates',
            'tenant_subscription_invoices',
            'personal_access_tokens',
            'sessions',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'permissions',
            'roles',
            'users',
            'tenants',
            'settings',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_title', 'value' => 'ZypCRM', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'INR', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'general'],
            ['key' => 'leave_policy_mode', 'value' => 'capped', 'group' => 'policy'],
            ['key' => 'leave_policy_cap_days_per_month', 'value' => '3', 'group' => 'policy'],
            ['key' => 'allow_registration', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'email_password_auth_enabled', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'firebase_enabled', 'value' => 'false', 'group' => 'firebase'],
            ['key' => 'firebase_phone_auth_enabled', 'value' => 'false', 'group' => 'auth'],
            ['key' => 'notification_email_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_push_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_whatsapp_enabled', 'value' => 'false', 'group' => 'notifications'],
            ['key' => 'notification_event_notice_broadcast_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_event_leave_status_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_event_fee_due_reminder_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_event_fee_payment_receipt_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'notification_event_subscription_expiry_enabled', 'value' => 'true', 'group' => 'notifications'],
            ['key' => 'whatsapp_provider_enabled', 'value' => 'false', 'group' => 'third_party'],
            ['key' => 'whatsapp_provider_name', 'value' => 'placeholder', 'group' => 'third_party'],
            ['key' => 'whatsapp_api_base_url', 'value' => '', 'group' => 'third_party'],
            ['key' => 'whatsapp_api_key', 'value' => '', 'group' => 'third_party'],
            ['key' => 'whatsapp_sender_id', 'value' => '', 'group' => 'third_party'],
            ['key' => 'enable_stripe', 'value' => 'false', 'group' => 'billing'],
            ['key' => 'enable_razorpay', 'value' => 'false', 'group' => 'billing'],
            ['key' => 'enable_manual_payment', 'value' => 'true', 'group' => 'billing'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        app(\App\Services\NotificationTemplateService::class)->seedDefaults();
    }

    private function seedPlatformPlans(): array
    {
        $starter = SubscriptionPlan::create([
            'name' => 'Starter',
            'description' => 'For small libraries',
            'price' => 1499,
            'billing_cycle' => 'monthly',
            'max_students' => 100,
            'features' => ['Up to 100 students', 'Basic reporting'],
            'is_active' => true,
        ]);

        $growth = SubscriptionPlan::create([
            'name' => 'Growth',
            'description' => 'For growing libraries',
            'price' => 2999,
            'billing_cycle' => 'monthly',
            'max_students' => 300,
            'features' => ['Up to 300 students', 'Advanced reporting', 'Priority support'],
            'is_active' => true,
        ]);

        SubscriptionPlan::create([
            'name' => 'Scale',
            'description' => 'For large operations',
            'price' => 24999,
            'billing_cycle' => 'yearly',
            'max_students' => 0,
            'features' => ['Unlimited students', 'Multi-branch ready', 'Dedicated support'],
            'is_active' => true,
        ]);

        return [$starter, $growth];
    }

    private function seedSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'email' => 'admin@zypcrm.test',
            'phone' => '9000000001',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);
    }

    private function seedTenants(): array
    {
        $tenantA = Tenant::create([
            'name' => 'Alpha Library',
            'status' => 'active',
            'email' => 'alpha@zypcrm.test',
            'phone' => '9000000101',
            'address' => 'Sector 62, Noida',
        ]);

        $tenantB = Tenant::create([
            'name' => 'Beta Reading Hub',
            'status' => 'active',
            'email' => 'beta@zypcrm.test',
            'phone' => '9000000201',
            'address' => 'Vijay Nagar, Indore',
        ]);

        return [$tenantA, $tenantB];
    }

    private function seedOwners(Tenant $tenantA, Tenant $tenantB): array
    {
        $ownerA = User::create([
            'name' => 'Owner Alpha',
            'email' => 'owner.alpha@zypcrm.test',
            'phone' => '9000000102',
            'password' => Hash::make('password123'),
            'role' => 'library_owner',
            'tenant_id' => $tenantA->id,
        ]);

        $ownerB = User::create([
            'name' => 'Owner Beta',
            'email' => 'owner.beta@zypcrm.test',
            'phone' => '9000000202',
            'password' => Hash::make('password123'),
            'role' => 'library_owner',
            'tenant_id' => $tenantB->id,
        ]);

        return [$ownerA, $ownerB];
    }

    private function seedStudents(Tenant $tenantA, Tenant $tenantB): array
    {
        $studentA1 = User::create([
            'name' => 'Student A One',
            'email' => 'student.a1@zypcrm.test',
            'phone' => '9000001101',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'tenant_id' => $tenantA->id,
        ]);

        $studentA2 = User::create([
            'name' => 'Student A Two',
            'email' => 'student.a2@zypcrm.test',
            'phone' => '9000001102',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'tenant_id' => $tenantA->id,
        ]);

        $studentB1 = User::create([
            'name' => 'Student B One',
            'email' => 'student.b1@zypcrm.test',
            'phone' => '9000002101',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'tenant_id' => $tenantB->id,
        ]);

        $studentDual = User::create([
            'name' => 'Student Dual',
            'email' => 'student.dual@zypcrm.test',
            'phone' => '9000009999',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'tenant_id' => $tenantA->id,
        ]);

        return [$studentA1, $studentA2, $studentB1, $studentDual];
    }

    private function seedMemberships(Tenant $tenantA, Tenant $tenantB, User $studentA1, User $studentA2, User $studentB1, User $studentDual): void
    {
        StudentMembership::updateOrCreate(['user_id' => $studentA1->id, 'tenant_id' => $tenantA->id], ['status' => 'active', 'joined_at' => now()->subMonths(4)]);
        StudentMembership::updateOrCreate(['user_id' => $studentA2->id, 'tenant_id' => $tenantA->id], ['status' => 'active', 'joined_at' => now()->subMonths(2)]);
        StudentMembership::updateOrCreate(['user_id' => $studentB1->id, 'tenant_id' => $tenantB->id], ['status' => 'active', 'joined_at' => now()->subMonths(3)]);
        StudentMembership::updateOrCreate(['user_id' => $studentDual->id, 'tenant_id' => $tenantA->id], ['status' => 'active', 'joined_at' => now()->subMonths(1)]);
        StudentMembership::updateOrCreate(['user_id' => $studentDual->id, 'tenant_id' => $tenantB->id], ['status' => 'active', 'joined_at' => now()->subWeeks(3)]);
    }

    private function seedSeats(Tenant $tenantA, Tenant $tenantB, User $studentA1, User $studentA2, User $studentB1): array
    {
        $seatA1 = Seat::create(['tenant_id' => $tenantA->id, 'name' => 'A-01', 'status' => 'occupied', 'user_id' => $studentA1->id]);
        $seatA2 = Seat::create(['tenant_id' => $tenantA->id, 'name' => 'A-02', 'status' => 'occupied', 'user_id' => $studentA2->id]);
        Seat::create(['tenant_id' => $tenantA->id, 'name' => 'A-03', 'status' => 'available']);
        Seat::create(['tenant_id' => $tenantA->id, 'name' => 'A-04', 'status' => 'maintenance']);

        $seatB1 = Seat::create(['tenant_id' => $tenantB->id, 'name' => 'B-01', 'status' => 'occupied', 'user_id' => $studentB1->id]);
        Seat::create(['tenant_id' => $tenantB->id, 'name' => 'B-02', 'status' => 'available']);
        Seat::create(['tenant_id' => $tenantB->id, 'name' => 'B-03', 'status' => 'available']);

        return [$seatA1, $seatA2, $seatB1];
    }

    private function seedLibraryPlans(Tenant $tenantA, Tenant $tenantB): array
    {
        $planA = LibraryPlan::create([
            'tenant_id' => $tenantA->id,
            'name' => 'Morning 6AM-2PM',
            'price' => 1200,
            'duration_days' => 30,
            'start_time' => '06:00:00',
            'end_time' => '14:00:00',
            'is_active' => true,
        ]);

        $planB = LibraryPlan::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Full Day',
            'price' => 1800,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        return [$planA, $planB];
    }

    private function seedTenantSubscriptions(Tenant $tenantA, Tenant $tenantB, array $platformPlans): void
    {
        Subscription::create([
            'tenant_id' => $tenantA->id,
            'subscription_plan_id' => $platformPlans[1]->id,
            'status' => 'active',
            'ends_at' => now()->addDays(21),
        ]);

        Subscription::create([
            'tenant_id' => $tenantB->id,
            'subscription_plan_id' => $platformPlans[0]->id,
            'status' => 'active',
            'ends_at' => now()->addDays(12),
        ]);
    }

    private function seedStudentSubscriptions(
        Tenant $tenantA,
        Tenant $tenantB,
        LibraryPlan $planA,
        LibraryPlan $planB,
        User $studentA1,
        User $studentA2,
        User $studentB1,
        User $studentDual,
        Seat $seatA1,
        Seat $seatA2,
        Seat $seatB1
    ): void {
        StudentSubscription::create([
            'tenant_id' => $tenantA->id,
            'user_id' => $studentA1->id,
            'library_plan_id' => $planA->id,
            'seat_id' => $seatA1->id,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'status' => 'active',
        ]);

        StudentSubscription::create([
            'tenant_id' => $tenantA->id,
            'user_id' => $studentA2->id,
            'library_plan_id' => $planA->id,
            'seat_id' => $seatA2->id,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(25)->toDateString(),
            'status' => 'active',
        ]);

        StudentSubscription::create([
            'tenant_id' => $tenantB->id,
            'user_id' => $studentB1->id,
            'library_plan_id' => $planB->id,
            'seat_id' => $seatB1->id,
            'start_date' => now()->subDays(8)->toDateString(),
            'end_date' => now()->addDays(22)->toDateString(),
            'status' => 'active',
        ]);

        StudentSubscription::create([
            'tenant_id' => $tenantA->id,
            'user_id' => $studentDual->id,
            'library_plan_id' => $planA->id,
            'seat_id' => null,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->addDays(27)->toDateString(),
            'status' => 'active',
        ]);
    }

    private function seedFeePayments(Tenant $tenantA, Tenant $tenantB, User $studentA1, User $studentA2, User $studentB1, User $studentDual): void
    {
        $rows = [
            [$tenantA->id, $studentA1->id, 1200, now()->subDays(1)->toDateString(), 'paid', 'online', 'txn_alpha_1001'],
            [$tenantA->id, $studentA2->id, 1200, now()->toDateString(), 'pending', null, null],
            [$tenantA->id, $studentDual->id, 900, now()->subDays(3)->toDateString(), 'overdue', null, null],
            [$tenantB->id, $studentB1->id, 1800, now()->subDays(2)->toDateString(), 'paid', 'cash', null],
            [$tenantB->id, $studentDual->id, 1800, now()->addDays(3)->toDateString(), 'pending', null, null],
        ];

        foreach ($rows as [$tenantId, $userId, $amount, $date, $status, $method, $txn]) {
            FeePayment::create([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'amount' => $amount,
                'platform_fee_amount' => 0,
                'net_amount' => $amount,
                'payment_date' => $date,
                'status' => $status,
                'payment_method' => $method,
                'transaction_id' => $txn,
                'remarks' => 'Demo seeded record',
                'slug' => Str::lower(Str::random(10)),
            ]);
        }
    }

    private function seedAttendance(Tenant $tenantA, Tenant $tenantB, User $studentA1, User $studentA2, User $studentB1, User $studentDual): void
    {
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->toDateString();

            StudentAttendance::create([
                'tenant_id' => $tenantA->id,
                'user_id' => $studentA1->id,
                'date' => $date,
                'status' => $i === 2 ? 'absent' : 'present',
                'check_in' => $i === 2 ? null : '06:10:00',
                'check_out' => $i === 2 ? null : '13:45:00',
            ]);

            StudentAttendance::create([
                'tenant_id' => $tenantA->id,
                'user_id' => $studentA2->id,
                'date' => $date,
                'status' => $i === 1 ? 'leave' : 'present',
                'check_in' => $i === 1 ? null : '06:20:00',
                'check_out' => $i === 1 ? null : '13:30:00',
            ]);

            StudentAttendance::create([
                'tenant_id' => $tenantB->id,
                'user_id' => $studentB1->id,
                'date' => $date,
                'status' => $i === 4 ? 'absent' : 'present',
                'check_in' => $i === 4 ? null : '08:00:00',
                'check_out' => $i === 4 ? null : '19:00:00',
            ]);

            StudentAttendance::create([
                'tenant_id' => $tenantA->id,
                'user_id' => $studentDual->id,
                'date' => $date,
                'status' => $i === 3 ? 'leave' : 'present',
                'check_in' => $i === 3 ? null : '07:00:00',
                'check_out' => $i === 3 ? null : '12:00:00',
            ]);
        }
    }

    private function seedLeaves(Tenant $tenantA, Tenant $tenantB, User $studentA1, User $studentB1): void
    {
        StudentLeave::create([
            'tenant_id' => $tenantA->id,
            'user_id' => $studentA1->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'reason' => 'Family function',
            'status' => 'pending',
        ]);

        StudentLeave::create([
            'tenant_id' => $tenantB->id,
            'user_id' => $studentB1->id,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->subDays(4)->toDateString(),
            'reason' => 'Medical leave',
            'status' => 'approved',
        ]);
    }

    private function seedSupportTickets(User $ownerA, User $ownerB, User $superAdmin): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $ownerA->id,
            'subject' => 'Need help with payment gateway setup',
            'status' => 'answered',
            'priority' => 'high',
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $ownerA->id,
            'message' => 'Stripe key is added but test checkout still fails.',
        ]);

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $superAdmin->id,
            'message' => 'Please verify webhook URL and live/test mode settings.',
        ]);

        SupportTicket::create([
            'user_id' => $ownerB->id,
            'subject' => 'How to migrate old students?',
            'status' => 'open',
            'priority' => 'medium',
        ]);
    }

    private function seedNotices(User $superAdmin, User $ownerA, Tenant $tenantA): void
    {
        Notice::create([
            'tenant_id' => null,
            'created_by' => $superAdmin->id,
            'title' => 'Platform Maintenance Window',
            'body' => 'Scheduled maintenance on Sunday 11:30 PM to 12:30 AM IST.',
            'level' => 'warning',
            'audience' => 'both',
            'delivery_in_app' => true,
            'delivery_email' => false,
            'delivery_push' => false,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(5),
        ]);

        Notice::create([
            'tenant_id' => $tenantA->id,
            'created_by' => $ownerA->id,
            'title' => 'Library Holiday Notice',
            'body' => 'Library will remain closed this Friday due to local holiday.',
            'level' => 'info',
            'audience' => 'students',
            'delivery_in_app' => true,
            'delivery_email' => false,
            'delivery_push' => false,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
        ]);
    }
}
