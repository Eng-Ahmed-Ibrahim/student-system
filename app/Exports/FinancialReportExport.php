<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class FinancialReportExport implements FromCollection, WithHeadings
{
    protected $payments;
    protected $studentFees;

    public function __construct(Collection $payments, Collection $studentFees)
    {
        $this->payments = $payments;
        $this->studentFees = $studentFees;
    }

    public function collection()
    {
        $rows = collect();

        // // إضافة المدفوعات
        // foreach ($this->payments as $payment) {
        //     $student = $payment->student;
        //     $rows->push([
        //         'نوع'               => 'مدفوع',
        //         'كود الطالب'        => $student->student_code ?? '---',
        //         'اسم الطالب'        => $student->name ?? '---',
        //         'المبلغ'            => $payment->amount,
        //         'التاريخ'           => $payment->payment_date,
        //         'الوصف'             => $payment->description ?? '---',
        //     ]);
        // }

        // إضافة المستحقات غير المدفوعة
        foreach ($this->studentFees as $fee) {
            $student = $fee->student;
            $remain= $fee->final_amount - $fee->payments_sum_amount;
            $rows->push([
                'نوع'               => 'مستحق غير مدفوع',
                'كود الطالب'        => $student->student_code ?? '---',
                'اسم الطالب'        => $student->name ?? '---',
                'المبلغ المستحق '            => $fee->final_amount,
                'المبلغ المدفوع'            => $fee->payments_sum_amount,
                'المتبقي'            => $remain,
                // 'التاريخ'           => $fee->date,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'نوع', 'كود الطالب', 'اسم الطالب', 'المبلغ المستحق ', 'المبلغ المدفوع', 'المتبقي' , 
        ];
    }
}
