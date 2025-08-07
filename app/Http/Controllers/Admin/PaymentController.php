<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'amount' => 'required|numeric|min:1',
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer',
    ]);

    // البحث عن الرسوم الخاصة بالشهر والسنة
    $studentFee = StudentFee::where('student_id', $request->student_id)
        ->where('month', $request->month)
        ->where('year', $request->year)
        ->first();

    if (!$studentFee) {
        return back()->with('error', 'لا توجد رسوم مسجلة لهذا الشهر');
    }

    // التأكد أن الدفع لا يتجاوز المستحق
    $paidSoFar = $studentFee->payments()->sum('amount');
    $remaining = $studentFee->amount - $paidSoFar;

    if ($request->amount > $remaining) {
        return back()->with('error', 'المبلغ المدفوع أكبر من المستحق');
    }

    Payment::create([
        'student_fee_id' => $studentFee->id,
        'amount' => $request->amount,
        'student_id'=>$request->student_id,
        'payment_date' => now(),
        'time' => now()->format('H:i:s'),
    ]);

    return back()->with('success', 'تمت العملية بنجاح');
}

}
