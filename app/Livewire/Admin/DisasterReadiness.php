<?php

namespace App\Livewire\Admin;

use App\Models\BackupSnapshot;
use App\Models\IncidentLog;
use App\Services\BackupService;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DisasterReadiness extends Component
{
    use WithPagination;

    public array $healthChecks = [];

    public function createSnapshot(): void
    {
        if (! Schema::hasTable('backup_snapshots')) {
            $this->dispatch('notify', type: 'error', message: 'Backup tables missing. Run migrations first.');

            return;
        }

        $snapshot = app(BackupService::class)->createSnapshot(auth()->id());
        $this->dispatch('notify', type: $snapshot->status === 'success' ? 'success' : 'error', message: $snapshot->status === 'success' ? 'Snapshot created.' : 'Snapshot failed. Check incident logs.');
    }

    public function restoreSnapshot(int $snapshotId): void
    {
        if (! Schema::hasTable('backup_snapshots')) {
            return;
        }

        $snapshot = BackupSnapshot::findOrFail($snapshotId);
        $ok = app(BackupService::class)->restoreSnapshot($snapshot, auth()->id());
        $this->dispatch('notify', type: $ok ? 'warning' : 'error', message: $ok ? 'Restore completed. Validate application data immediately.' : 'Restore failed. Check incident logs.');
    }

    public function runHealthChecks(): void
    {
        $this->healthChecks = app(BackupService::class)->runHealthChecks(auth()->id());
        $hasErrors = collect($this->healthChecks)->contains(fn ($row) => in_array($row['status'], ['error', 'warning'], true));
        $this->dispatch('notify', type: $hasErrors ? 'warning' : 'success', message: $hasErrors ? 'Health checks completed with warnings.' : 'All health checks are healthy.');
    }

    public function render()
    {
        $snapshots = collect();
        $incidents = collect();

        if (Schema::hasTable('backup_snapshots')) {
            $snapshots = BackupSnapshot::query()->latest()->paginate(10, ['*'], 'snapshots');
        }

        if (Schema::hasTable('incident_logs')) {
            $incidents = IncidentLog::query()->latest('occurred_at')->latest()->paginate(10, ['*'], 'incidents');
        }

        return view('livewire.admin.disaster-readiness', [
            'snapshots' => $snapshots,
            'incidents' => $incidents,
        ]);
    }
}
