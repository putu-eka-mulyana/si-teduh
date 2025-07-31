<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_number',
        'nik',
        'bpjs_number',
        'phone_number',
        'fullname',
        'gender',
        'birthday',
        'job_title',
        'address',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'owner_id');
    }
}
