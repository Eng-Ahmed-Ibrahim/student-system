<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Log;

class StudentService
{

    public function generate_genral_monthly_fee()
    {
        $students = Student::with(['group'])->get();
        foreach ($students as $student) {
            $this->generateMonthlyFeeIfNotExists($student);
        }
    }
    public function generateMonthlyFeeIfNotExists($student)
    {
        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;

        $group = $student->group;
        if (!$group) {
            return false;
        }

        // مثلاً نفرض رسوم الاشتراك 300
        $monthlyFee = $group->monthly_fee ?? 0;

        // لو فيه سجل بالفعل ما تعملش حاجة
        $exists = StudentFee::where('student_id', $student->id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if (!$exists) {
            StudentFee::create([
                'student_id' => $student->id,
                'group_id' => $group->id,
                'amount' => $monthlyFee,
                'status' => 'unpaid',
                'month' => $month,
                'year' => $year,
            ]);
            Log::info("Created fee for student {$student->id} for $month/$year");
        }
        return true;
    }
}
