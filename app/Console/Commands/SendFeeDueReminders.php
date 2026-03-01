<?php

namespace App\Console\Commands;

use App\Models\FeePayment;
use App\Models\Setting;
use App\Notifications\FeeDueReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendFeeDueReminders extends Command
{
    protected $signature = 'library:send-fee-reminders';

    protected $description = 'Send fee due/overdue reminders to students with pending payments.';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();
        $count = 0;

        $payments = FeePayment::query()
            ->with(['user', 'tenant'])
            ->whereIn('status', ['pending', 'overdue'])
            ->whereDate('payment_date', '<=', $today)
            ->whereNotNull('slug')
            ->get();

        foreach ($payments as $payment) {
            if (! $payment->user || empty($payment->user->email)) {
                continue;
            }

            $cacheKey = "fee_reminder:{$payment->id}:{$today}";
            if (! Cache::add($cacheKey, true, now()->addDay())) {
                continue;
            }

            $payment->user->notify(new FeeDueReminderNotification($payment));
            $count++;
        }

        $runAt = now()->toDateTimeString();
        Setting::updateOrCreate(['key' => 'fee_reminders_last_run_at'], ['value' => $runAt, 'group' => 'system']);
        Setting::updateOrCreate(['key' => 'scheduler_last_run_at'], ['value' => $runAt, 'group' => 'system']);
        Setting::updateOrCreate(['key' => 'scheduler_last_run_command'], ['value' => $this->signature, 'group' => 'system']);

        $this->info("Sent {$count} fee reminder notification(s).");

        return self::SUCCESS;
    }
}
