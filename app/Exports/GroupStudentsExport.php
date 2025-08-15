<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class GroupStudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $students;
    protected $group_name;

    public function __construct($students,$group_name)
    {
        $this->students = $students;
        $this->group_name = $group_name;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'كود الطالب',
            'الاسم',
            'رقم التلفون',
            ' رقم تلفون ولي الامر',
            'الرقم القومي',
            'الصف',
            "المجموعه"
        ];
    }

    public function map($student): array
    {
        return [
            $student->student_code,
            $student->name,
            $student->phone,
            $student->parent_phone,
            $student->national_id,
            match($student->grade_level) {
                "1" => 'الصف الأول الثانوي',
                "2" => 'الصف الثاني الثانوي',
                "3" => 'الصف الثالث الثانوي',
                default => 'غير معروف'
            },
            $this->group_name,
        ];
    }
}
