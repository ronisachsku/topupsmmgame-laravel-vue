<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function durationOther()
    {
        return $this->hasMany(ServiceDurationOther::class);
    }

    public function sosmed()
    {
        return $this->hasMany(Sosmed::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
