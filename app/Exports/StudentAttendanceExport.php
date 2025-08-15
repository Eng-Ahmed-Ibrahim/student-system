<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentAttendanceExport implements FromCollection, WithHeadings
{
    protected $attendances;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            $student = $attendance->student; // لازم تكون محمل علاقة student في الكويري
            return [
                'كود الطالب' => $student->student_code ?? '---',
                'اسم الطالب' => $student->name ?? '---',
                'رقم التلفون' => $student->phone ?? '---',
                'رقم ولي الأمر' => $student->parent_phone ?? '---',
                'التاريخ' => $attendance->date,
                'وقت تسجيل الحضور' => $attendance->time
                    ? \Carbon\Carbon::parse($attendance->time)->format('h:i A')
                    : 'لم يحضر',
                'موعد الحصة' => \Carbon\Carbon::parse($attendance->class_start_at)->format('h:i A'),
                'الحالة' => $attendance->status ? 'حضور' : 'غياب',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'كود الطالب',
            'اسم الطالب',
            'رقم التلفون',
            'رقم ولي الأمر',
            'التاريخ',
            'وقت تسجيل الحضور',
            'موعد الحصة',
            'الحالة'
        ];
    }
}
