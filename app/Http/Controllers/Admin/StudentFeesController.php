<?php

namespace App\Http\Controllers\Admin;

use App\Models\StudentFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentFeesController extends Controller
{
   public function update(Request $request, $id)
    {
        $fee = StudentFee::findOrFail($id);
        
        if ($fee->status == 'paid') {
            return redirect()->back()->with('error', 'لا يمكن تعديل رسوم مدفوعة بالفعل.');
        }
        
        $request->validate([
            'final_amount' => 'required|numeric|min:0',
        ], [
            'final_amount.required' => 'يجب إدخال قيمة المبلغ.',
            'final_amount.numeric' => 'قيمة المبلغ يجب أن تكون رقمية.',
            'final_amount.min' => 'قيمة المبلغ يجب ألا تقل عن صفر.',
        ]);
        
        $fee->update([
            'final_amount' => $request->final_amount,
        ]);

        return redirect()->back()->with('success', 'تم تعديل المبلغ بنجاح.');
    }

    /**
     * حذف رسوم الطالب
     */
    public function destroy($id)
    {
        $fee = StudentFee::findOrFail($id);

        // منع الحذف لو الحالة مدفوعة
        if ($fee->status == 'paid') {
            return redirect()->back()->with('error', 'لا يمكن حذف رسوم تم دفعها.');
        }

        $fee->delete();

    }
}
