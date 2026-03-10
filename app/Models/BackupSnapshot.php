<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSnapshot extends Model
{
    protected $fillable = [
        'name',
        'disk',
        'file_path',
        'size_bytes',
        'status',
        'checksum',
        'notes',
        'created_by',
        'restored_at',
    ];

    protected $casts = [
        'restored_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
