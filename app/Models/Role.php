<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasAuditLog;
}
