<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamResultsExport implements FromCollection, WithHeadings
{
    protected $results;

    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results->map(function ($result) {
            return [
                'كود الطالب' => $result->student->student_code ?? '',
                'اسم الطالب' => $result->student->name ?? '',
                'الصف'       => $result->student->grade_level ?? '',
                'المجموعة'    => $result->student->group->name ?? '',
                'اسم الامتحان' => $result->exam->name ?? '',
                'تاريخ الامتحان' => $result->exam->exam_date ?? '',
                'الدرجة'     => $result->score ?? '',
                'الدرجة النهائية' => $result->exam->total_score ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'كود الطالب',
            'اسم الطالب',
            'الصف',
            'المجموعة',
            'اسم الامتحان',
            'تاريخ الامتحان',
            'الدرجة',
            'الدرجة النهائية',
        ];
    }
}
