<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupSnapshot;
use Illuminate\Support\Facades\Storage;

class DisasterReadinessController extends Controller
{
    public function downloadSnapshot(BackupSnapshot $snapshot)
    {
        $disk = $snapshot->disk ?: 'local';
        if (! Storage::disk($disk)->exists($snapshot->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($snapshot->file_path, basename($snapshot->file_path));
    }
}
