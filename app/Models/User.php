<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasFactory;

    protected $fillable = [
        'phone_number',
        'password',
        'role',
        'owner_id',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi berdasarkan role
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'owner_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'owner_id');
    }
}
