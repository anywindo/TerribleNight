<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory, HasAuditLog;

    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
