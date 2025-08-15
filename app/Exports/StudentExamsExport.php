<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentExamsExport implements FromCollection, WithHeadings
{
    protected $exams;
    protected $student;

    public function __construct(Collection $exams , $student)
    {
        $this->exams = $exams;
        $this->student = $student;
    }

    public function collection()
    {
        return $this->exams->map(function ($result) {
            return [
                'اسم الطالب'          => $this->student->name ?? '',
                'كود الطالب'          => $this->student->student_code ?? '',
                'رقم الهاتف'          => $this->student->phone ?? '',
                'رقم هاتف ولي الأمر'  => $this->student->parent_phone ?? '',
                'تاريخ الامتحان'      => $result->exam->exam_date ?? '',
                'الدرجة'              => $result->score ?? '',
                'من'                   => $result->exam->total_score ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'اسم الطالب',
            'كود الطالب',
            'رقم الهاتف',
            'رقم هاتف ولي الأمر',
            'تاريخ الامتحان',
            'الدرجة',
            'من'
        ];
    }
}
