<?php

namespace App\Exports\Admin;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $rowNumber = 0;
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query(): Builder|\Illuminate\Database\Query\Builder
    {
        $query = Attendance::with(['user.location', 'shift', 'events'])->orderBy('date', 'desc');

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No.',
            'No.Karyawan (ID)',
            'Nama karyawan',
            'Cost Center',
            'Nama Lokasi Kerja',
            'Tanggal',
            'Shift',

            'Masuk Aktual',
            'Keluar Aktual',
        ];
    }

    public function map($attendance): array
    {
        $this->rowNumber++;

        $checkInEvent = $attendance->events->where('event_type', \App\Enums\EventType::START_SHIFT)->first();
        $checkOutEvent = $attendance->events->where('event_type', \App\Enums\EventType::END_SHIFT)->first();

        return [
            $this->rowNumber,
            $attendance->user->nik ?? $attendance->user->id ?? '-',
            $attendance->user->name ?? '-',
            '-', // Cost Center
            $attendance->user->location->name ?? '-',
            $attendance->date ? $attendance->date->format('Y-m-d') : '-',
            $attendance->shift ? $attendance->shift->shift_name : '-',

            $checkInEvent ? $checkInEvent->timestamp->format('H:i:s') : '-',
            $checkOutEvent ? $checkOutEvent->timestamp->format('H:i:s') : '-',
        ];
    }
}
