<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class OpsMonitor extends Component
{
    use WithPagination;

    public string $failedSearch = '';

    public function retryFailedJob(int $id): void
    {
        if (! Schema::hasTable('failed_jobs')) {
            return;
        }

        Artisan::call('queue:retry', ['id' => [$id]]);
        $this->dispatch('notify', type: 'success', message: "Retry triggered for failed job #{$id}.");
    }

    public function forgetFailedJob(int $id): void
    {
        if (! Schema::hasTable('failed_jobs')) {
            return;
        }

        DB::table('failed_jobs')->where('id', $id)->delete();
        $this->dispatch('notify', type: 'success', message: "Failed job #{$id} removed.");
    }

    public function retryAllFailedJobs(): void
    {
        if (! Schema::hasTable('failed_jobs')) {
            return;
        }

        Artisan::call('queue:retry', ['id' => ['all']]);
        $this->dispatch('notify', type: 'success', message: 'Retry triggered for all failed jobs.');
    }

    public function flushFailedJobs(): void
    {
        if (! Schema::hasTable('failed_jobs')) {
            return;
        }

        Artisan::call('queue:flush');
        $this->dispatch('notify', type: 'success', message: 'All failed jobs were cleared.');
    }

    public function render()
    {
        $queueStats = $this->queueStats();
        $providerHealth = $this->providerHealth();

        $failedJobs = collect();
        if (Schema::hasTable('failed_jobs')) {
            $query = DB::table('failed_jobs')->orderByDesc('failed_at');
            if (trim($this->failedSearch) !== '') {
                $search = trim($this->failedSearch);
                $query->where(function ($q) use ($search): void {
                    $q->where('queue', 'like', '%'.$search.'%')
                        ->orWhere('exception', 'like', '%'.$search.'%')
                        ->orWhere('id', 'like', '%'.$search.'%');
                });
            }
            $failedJobs = $query->paginate(10);
        }

        return view('livewire.admin.ops-monitor', [
            'queueStats' => $queueStats,
            'providerHealth' => $providerHealth,
            'failedJobs' => $failedJobs,
        ]);
    }

    private function queueStats(): array
    {
        $pendingCount = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $failedCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $queues = Schema::hasTable('jobs')
            ? DB::table('jobs')->select('queue', DB::raw('COUNT(*) as total'))->groupBy('queue')->orderByDesc('total')->get()
            : collect();

        return [
            'pending' => $pendingCount,
            'failed' => $failedCount,
            'queues' => $queues,
        ];
    }

    private function providerHealth(): array
    {
        return [
            'mail' => $this->mailHealth(),
            'push' => $this->pushHealth(),
            'whatsapp' => $this->whatsappHealth(),
        ];
    }

    private function mailHealth(): array
    {
        $enabled = Setting::getBool('notification_email_enabled', true);
        $host = Setting::getValue('mail_host', (string) config('mail.mailers.smtp.host'));
        $port = (int) (Setting::getValue('mail_port', (string) config('mail.mailers.smtp.port')) ?: 0);

        if (! $enabled) {
            return ['status' => 'disabled', 'message' => 'Email notifications are disabled in settings.'];
        }

        if ($host === null || trim($host) === '' || $port <= 0) {
            return ['status' => 'warning', 'message' => 'SMTP host/port is missing.'];
        }

        $conn = @fsockopen($host, $port, $errno, $errstr, 2);
        if (! $conn) {
            return ['status' => 'error', 'message' => "SMTP unreachable ({$host}:{$port}) - {$errstr}"];
        }
        fclose($conn);

        return ['status' => 'healthy', 'message' => "SMTP reachable ({$host}:{$port})."];
    }

    private function pushHealth(): array
    {
        $pushEnabled = Setting::getBool('notification_push_enabled', true);
        $firebaseEnabled = Setting::getBool('firebase_enabled', false);
        $serviceJson = trim((string) Setting::getValue('firebase_service_account_json', ''));

        if (! $pushEnabled) {
            return ['status' => 'disabled', 'message' => 'Push notifications are disabled in settings.'];
        }

        if (! $firebaseEnabled) {
            return ['status' => 'warning', 'message' => 'Firebase is not enabled.'];
        }

        if ($serviceJson === '') {
            return ['status' => 'warning', 'message' => 'Firebase service account JSON is missing.'];
        }

        json_decode($serviceJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['status' => 'error', 'message' => 'Firebase service account JSON is invalid.'];
        }

        return ['status' => 'healthy', 'message' => 'Firebase config looks valid for push notifications.'];
    }

    private function whatsappHealth(): array
    {
        $enabled = Setting::getBool('notification_whatsapp_enabled', false);
        $providerEnabled = Setting::getBool('whatsapp_provider_enabled', false);
        $providerName = Setting::getValue('whatsapp_provider_name', 'placeholder');
        $baseUrl = trim((string) Setting::getValue('whatsapp_api_base_url', ''));
        $apiKey = trim((string) Setting::getValue('whatsapp_api_key', ''));

        if (! $enabled) {
            return ['status' => 'disabled', 'message' => 'WhatsApp notifications are disabled in settings.'];
        }

        if (! $providerEnabled) {
            return ['status' => 'warning', 'message' => 'WhatsApp provider is not enabled.'];
        }

        if ($baseUrl === '' || $apiKey === '') {
            return ['status' => 'warning', 'message' => "Provider '{$providerName}' is missing API base URL or API key."];
        }

        return ['status' => 'healthy', 'message' => "Provider '{$providerName}' is configured."];
    }
}
