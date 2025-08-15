<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentPaymentsExport implements FromCollection, WithHeadings
{
    protected $payments;
    protected $student;

    public function __construct(Collection $payments, $student)
    {
        $this->payments = $payments;
        $this->student = $student;
    }

    public function collection()
    {
        return $this->payments->map(function ($payment) {
            return [
                'اسم الطالب'         => $this->student->name ?? '',
                'كود الطالب'         => $this->student->student_code ?? '',
                'رقم الهاتف'         => $this->student->phone ?? '',
                'رقم ولي الأمر'      => $this->student->parent_phone ?? '',
                'تاريخ الدفع'        => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d'),
                'وقت الدفع'          => $payment->time ? \Carbon\Carbon::parse($payment->time)->format('h:i A') : '',
                'المبلغ'             => $payment->amount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'اسم الطالب',
            'كود الطالب',
            'رقم الهاتف',
            'رقم ولي الأمر',
            'تاريخ الدفع',
            'وقت الدفع',
            'المبلغ'
        ];
    }
}
