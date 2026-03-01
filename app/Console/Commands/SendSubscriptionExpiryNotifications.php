<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendSubscriptionExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:notify-expiring-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to students whose library subscriptions expire in 3 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = \Carbon\Carbon::today()->addDays(3);

        $subscriptions = \App\Models\StudentSubscription::with(['user', 'plan', 'tenant'])
            ->where('status', 'active')
            ->whereDate('end_date', $targetDate)
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            if (! $subscription->user || empty($subscription->user->email)) {
                continue;
            }

            $cacheKey = "subscription_expiry_notice:{$subscription->id}:{$targetDate->toDateString()}";
            if (! Cache::add($cacheKey, true, now()->addDays(4))) {
                continue;
            }

            $subscription->user->notify(new \App\Notifications\SubscriptionExpiryNotification($subscription));
            $count++;
        }

        $runAt = now()->toDateTimeString();
        Setting::updateOrCreate(['key' => 'subscription_expiry_last_run_at'], ['value' => $runAt, 'group' => 'system']);
        Setting::updateOrCreate(['key' => 'scheduler_last_run_at'], ['value' => $runAt, 'group' => 'system']);
        Setting::updateOrCreate(['key' => 'scheduler_last_run_command'], ['value' => $this->signature, 'group' => 'system']);

        $this->info("Sent {$count} subscription expiry notifications for date: {$targetDate->format('Y-m-d')}");
    }
}
