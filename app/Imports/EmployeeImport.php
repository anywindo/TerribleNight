<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class EmployeeImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        return new User([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'phone'    => $row['phone'] ?? null,
            'nik'      => $row['nik'] ?? null,
            'password' => Hash::make($row['password'] ?? 'password'), // Default password
            'is_active'=> true,
        ]);
    }
}
