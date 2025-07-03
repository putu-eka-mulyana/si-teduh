<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{

    protected $fillable = [
        'fullname',
        'position',
    ];

    // Relasi: Officer memiliki banyak schedule
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
