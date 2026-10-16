<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, HasAuditLog;
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
