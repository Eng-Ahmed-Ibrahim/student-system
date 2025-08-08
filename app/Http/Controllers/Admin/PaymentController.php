<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{




    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $year = now()->year;
        $student_id = $request->student_id;
        $amountToDistribute = $request->amount;
        // كل الرسوم لهذا الطالب مرتبة حسب التاريخ أو الشهر
        $fees = StudentFee::where('student_id', $student_id)
            ->where('year', $year)
            ->orderBy('month')
            ->where("status",'unpaid')
            ->get();

        DB::beginTransaction();

        try {
            foreach ($fees as $fee) {
                if ($amountToDistribute <= 0) break;

                $paidForThisFee = $fee->payments()->sum('amount'); // مدفوعات للشهر بيجمعها 
                $remainingForThisFee = $fee->final_amount - $paidForThisFee; //  لو مدفوعات الشهر ده ناقص المدفوع بيساوي 0 يبقي كده الشهر ادفع لو لا يبقي الشهر الحالي عليه الدور يدفع

                if ($remainingForThisFee <= 0) continue; // هذا الشهر مدفوع بالفعل

                // نحسب المبلغ اللي هيندفع للشهر ده
                $paymentAmount = min($remainingForThisFee, $amountToDistribute); // هنا انت بتجيب اصغر قيمه لو كان المدفوع اكبر من قيمه الشهر ياخد قيمه المدفوع من الشهر

                // حفظ الدفعه
                Payment::create([
                    'student_fee_id' => $fee->id,
                    'amount' => $paymentAmount,
                    'student_id' => $student_id,
                    'payment_date' => now(),
                    'time' => now()->format('H:i:s'),
                ]);

                $amountToDistribute -= $paymentAmount; // بتنقص فيمه القيمه الي هتدفع لو قيمه الشهر ٢٠٠ وهو دفع ٣٠٠ ف هينفص ٢٠٠ يتبقي ١٠٠ للشهر الي جاي

                // لو اتسدد المبلغ بالكامل، نحدث status
                if (($paidForThisFee + $paymentAmount) >= $fee->final_amount) {
                    $fee->update(['status' => 'paid']);
                }
            }

            DB::commit();
            return back()->with('success', 'تمت العملية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء الحفظ');
        }
    }
}
