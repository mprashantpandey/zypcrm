<?php

namespace App\Services;

use App\Models\BackupSnapshot;
use App\Models\IncidentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public function createSnapshot(?int $userId = null): BackupSnapshot
    {
        $timestamp = now()->format('Ymd_His');
        $filename = "snapshot_{$timestamp}.sql";
        $dir = 'backups';
        $relativePath = $dir.'/'.$filename;
        $absolutePath = storage_path('app/'.$relativePath);

        if (! is_dir(dirname($absolutePath))) {
            @mkdir(dirname($absolutePath), 0775, true);
        }

        $db = config('database.default');
        $cfg = config("database.connections.{$db}", []);

        $host = (string) ($cfg['host'] ?? '127.0.0.1');
        $port = (int) ($cfg['port'] ?? 3306);
        $database = (string) ($cfg['database'] ?? '');
        $username = (string) ($cfg['username'] ?? '');
        $password = (string) ($cfg['password'] ?? '');

        $status = 'success';
        $notes = null;

        $command = sprintf(
            'mysqldump --host=%s --port=%d --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($absolutePath)
        );

        $result = Process::timeout(120)->run($command);
        if (! $result->successful() || ! file_exists($absolutePath)) {
            $status = 'failed';
            $notes = trim(($result->errorOutput() ?: 'mysqldump failed; no output generated.'));
            Storage::disk('local')->put(str_replace('.sql', '.json', $relativePath), json_encode([
                'generated_at' => now()->toDateTimeString(),
                'database' => $database,
                'fallback' => true,
                'table_counts' => $this->collectTableCounts(),
            ], JSON_PRETTY_PRINT));
            $relativePath = str_replace('.sql', '.json', $relativePath);
            $absolutePath = storage_path('app/'.$relativePath);
        }

        $size = file_exists($absolutePath) ? (int) filesize($absolutePath) : 0;
        $checksum = file_exists($absolutePath) ? hash_file('sha256', $absolutePath) : null;

        $snapshot = BackupSnapshot::create([
            'name' => pathinfo($relativePath, PATHINFO_FILENAME),
            'disk' => 'local',
            'file_path' => $relativePath,
            'size_bytes' => $size,
            'status' => $status,
            'checksum' => $checksum,
            'notes' => $notes,
            'created_by' => $userId,
        ]);

        $this->logIncident(
            level: $status === 'success' ? 'info' : 'error',
            category: 'backup',
            title: $status === 'success' ? 'Backup snapshot created' : 'Backup snapshot failed',
            message: $status === 'success' ? "Snapshot {$snapshot->name} created." : ($notes ?: 'Snapshot creation failed.'),
            meta: ['snapshot_id' => $snapshot->id, 'path' => $relativePath],
            userId: $userId
        );

        return $snapshot;
    }

    public function restoreSnapshot(BackupSnapshot $snapshot, ?int $userId = null): bool
    {
        $absolutePath = storage_path('app/'.$snapshot->file_path);
        if (! file_exists($absolutePath) || ! str_ends_with($absolutePath, '.sql')) {
            $this->logIncident('error', 'restore', 'Snapshot restore failed', 'Snapshot file is missing or not SQL.', ['snapshot_id' => $snapshot->id], $userId);

            return false;
        }

        $db = config('database.default');
        $cfg = config("database.connections.{$db}", []);

        $host = (string) ($cfg['host'] ?? '127.0.0.1');
        $port = (int) ($cfg['port'] ?? 3306);
        $database = (string) ($cfg['database'] ?? '');
        $username = (string) ($cfg['username'] ?? '');
        $password = (string) ($cfg['password'] ?? '');

        $snapshot->update(['status' => 'restoring']);

        $command = sprintf(
            'mysql --host=%s --port=%d --user=%s --password=%s %s < %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($absolutePath)
        );

        $result = Process::timeout(180)->run($command);
        if (! $result->successful()) {
            $snapshot->update([
                'status' => 'failed',
                'notes' => trim($result->errorOutput() ?: 'Restore command failed.'),
            ]);

            $this->logIncident('error', 'restore', 'Snapshot restore failed', $snapshot->notes, ['snapshot_id' => $snapshot->id], $userId);

            return false;
        }

        $snapshot->update([
            'status' => 'success',
            'restored_at' => now(),
            'notes' => 'Restore completed successfully.',
        ]);

        $this->logIncident('warning', 'restore', 'Snapshot restored', "Snapshot {$snapshot->name} restored successfully.", ['snapshot_id' => $snapshot->id], $userId);

        return true;
    }

    public function runHealthChecks(?int $userId = null): array
    {
        $checks = [];

        try {
            DB::select('SELECT 1');
            $checks[] = ['key' => 'database', 'status' => 'healthy', 'message' => 'Database connection ok.'];
        } catch (\Throwable $e) {
            $checks[] = ['key' => 'database', 'status' => 'error', 'message' => 'Database check failed: '.$e->getMessage()];
        }

        $writable = is_writable(storage_path('app'));
        $checks[] = ['key' => 'storage', 'status' => $writable ? 'healthy' : 'error', 'message' => $writable ? 'Storage writable.' : 'Storage is not writable.'];

        $failedJobs = \Illuminate\Support\Facades\Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $checks[] = ['key' => 'queue_failed_jobs', 'status' => $failedJobs > 0 ? 'warning' : 'healthy', 'message' => "Failed jobs: {$failedJobs}"];

        $latestSnapshot = BackupSnapshot::query()->latest()->first();
        if ($latestSnapshot) {
            $hours = $latestSnapshot->created_at->diffInHours(now());
            $checks[] = [
                'key' => 'backup_freshness',
                'status' => $hours > 24 ? 'warning' : 'healthy',
                'message' => "Latest snapshot {$hours}h ago ({$latestSnapshot->name}).",
            ];
        } else {
            $checks[] = ['key' => 'backup_freshness', 'status' => 'warning', 'message' => 'No backup snapshots found.'];
        }

        foreach ($checks as $check) {
            if (in_array($check['status'], ['warning', 'error'], true)) {
                $this->logIncident($check['status'], 'health', 'Health check '.$check['status'], $check['message'], ['key' => $check['key']], $userId);
            }
        }

        return $checks;
    }

    private function collectTableCounts(): array
    {
        $tables = DB::select('SHOW TABLES');
        $counts = [];
        foreach ($tables as $row) {
            $table = array_values((array) $row)[0] ?? null;
            if ($table) {
                $counts[$table] = DB::table($table)->count();
            }
        }

        return $counts;
    }

    private function logIncident(string $level, string $category, string $title, ?string $message, array $meta = [], ?int $userId = null): void
    {
        if (! Schema::hasTable('incident_logs')) {
            return;
        }

        IncidentLog::create([
            'level' => $level,
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'meta' => $meta,
            'user_id' => $userId,
            'occurred_at' => now(),
        ]);
    }
}
