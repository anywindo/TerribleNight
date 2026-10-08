<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, HasAuditLog;

    /**
     * The guard name for Spatie permissions.
     */
    protected string $guard_name = 'sanctum';

    /**
     * Mass assignable attributes.
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'employee_id',
        'nik',
        'location_id',
        'direct_supervisor_id',
        'immediate_manager_id',
        'avatar',
        'is_active',
    ];

    /**
     * Hidden from serialization (never expose in JSON responses).
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // =========================================================================
    // Relationships
    // =========================================================================

    public function directSupervisor()
    {
        return $this->belongsTo(User::class, 'direct_supervisor_id');
    }

    public function immediateManager()
    {
        return $this->belongsTo(User::class, 'immediate_manager_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function overtimeRequests()
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function businessTripRequests()
    {
        return $this->hasMany(BusinessTripRequest::class);
    }

    public function shiftChangeRequests()
    {
        return $this->hasMany(ShiftChangeRequest::class);
    }

    public function attendanceCorrectionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function isAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'HR']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}
