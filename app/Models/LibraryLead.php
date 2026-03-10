<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryLead extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'message',
        'source',
        'status',
        'notes',
        'contacted_at',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
