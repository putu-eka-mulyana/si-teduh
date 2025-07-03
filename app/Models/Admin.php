<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{

    use HasFactory;
    protected $fillable = [
        'fullname',
        'jobtitle',
    ];

    // Relasi dengan User
    public function users()
    {
        return $this->hasMany(User::class, 'owner_id');
    }
}
