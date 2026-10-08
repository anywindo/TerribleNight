<?php

namespace App\Exports\Admin;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;

class EmployeeExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $rowNumber = 0;

    public function query(): Builder|\Illuminate\Database\Query\Builder
    {
        return User::with(['location', 'roles'])->orderBy('name', 'asc');
    }

    public function headings(): array
    {
        return [
            'No.',
            'ID Karyawan',
            'NIK',
            'Nama Lengkap',
            'Email',
            'Lokasi Kerja',
            'Status',
            'Role',
        ];
    }

    public function map($employee): array
    {
        $this->rowNumber++;

        $roles = $employee->roles->pluck('name')->join(', ');

        return [
            $this->rowNumber,
            'EMP' . str_pad($employee->id, 4, '0', STR_PAD_LEFT),
            $employee->nik ?? '-',
            $employee->name,
            $employee->email,
            $employee->location->name ?? '-',
            $employee->is_active ? 'Aktif' : 'Nonaktif',
            $roles ?: 'Karyawan',
        ];
    }
}
