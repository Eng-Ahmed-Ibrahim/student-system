<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceTableExport implements FromCollection, WithHeadings
{
    protected $attendances;
    protected $grades;

    public function __construct(Collection $attendances, array $grades)
    {
        $this->attendances = $attendances;
        $this->grades = $grades;
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            $student = $attendance->student;
            return [
                'كود الطالب'       => $student->student_code ?? '---',
                'اسم الطالب'       => $student->name ?? '---',
                'الصف'             => $this->grades[$student->grade_level - 1] ?? '---',
                'المجموعة'         => $student->group->name ?? '---',
                'رقم التلفون'      => $student->phone ?? '---',
                'رقم ولي الأمر'    => $student->parent_phone ?? '---',
                'التاريخ'          => $attendance->date,
                'الحالة'           => $attendance->status == 1 ? 'حاضر' : 'غائب',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'كود الطالب', 'اسم الطالب', 'الصف', 'المجموعة', 'رقم التلفون',
            'رقم ولي الأمر', 'التاريخ', 'الحالة'
        ];
    }
}
