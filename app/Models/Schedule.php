<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'datetime',
        'officer_id',
        'status',
        'type',
        'message',
    ];

    // Schedule milik Patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Schedule milik Officer
    public function officer()
    {
        return $this->belongsTo(Officer::class);
    }
}
