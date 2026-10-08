<?php

namespace App\Repositories\Eloquent;

use App\Models\Shift;
use App\Repositories\Contracts\ShiftRepositoryInterface;

class ShiftRepository extends BaseRepository implements ShiftRepositoryInterface
{
    protected string $model = Shift::class;
}
