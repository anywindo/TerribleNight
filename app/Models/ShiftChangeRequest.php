<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftChangeRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function originalShift()
    {
        return $this->belongsTo(Shift::class, 'original_shift_id');
    }

    public function newShift()
    {
        return $this->belongsTo(Shift::class, 'new_shift_id');
    }
}
