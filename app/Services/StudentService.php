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
        $today = Carbon::now();
        if ($today->lessThan(Carbon::create(2025, 8, 24))) {
            Log::info("No fees generated before 24/8/2025.");
            return;
        }

        if ($today->month == 8 && $today->day >= 24) {
            // من 24/8 لآخر 8 → يعتبر شهر 9
            $month = 9;
            $year = $today->year;
        } else {
            // عادي: الشهر الحالي
            $month = $today->month;
            $year = $today->year;
        }
        Student::with('group')->chunk(500, function ($studentsChunk) use ($month, $year) {
            $studentIds = $studentsChunk->pluck('id')->toArray();

            $existingFees = StudentFee::whereIn('student_id', $studentIds)
                ->where('month', $month)
                ->where('year', $year)
                ->pluck('student_id')
                ->toArray();

            $insertData = [];

            foreach ($studentsChunk as $student) {
                // استخدم نفس منطق generateMonthlyFeeIfNotExists
                if (in_array($student->id, $existingFees) || !$student->group) {
                    continue;
                }
                $monthlyFee = $student->group->monthly_fee ?? 0;
                $discount = is_numeric($student->discount) ? min(max($student->discount, 0), 100) : 0;
                $discount_amount = ($monthlyFee * ($discount  / 100));
                $final_amount = $monthlyFee - $discount_amount ;

                $insertData[] = [
                    'student_id' => $student->id,
                    // @phpstan-ignore-next-line
                    'group_id' => $student->group->id,
                    'amount' => $monthlyFee,
                    'status' => 'unpaid',
                    'month' => $month,
                    'year' => $year,
                    'final_amount' => $final_amount,
                    'discount' => $discount_amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'date' => now()->toDateString(),
                ];
            }

            if (!empty($insertData)) {
                StudentFee::insert($insertData);
                Log::info("Inserted " . count($insertData) . " monthly fees in chunk for $month/$year.");
            }
        });

        Log::info("Finished generating all monthly fees for $month/$year.");
    }

    // تفضل موجودة زي ما هي لمعالجة طالب واحد
    public function generateMonthlyFeeIfNotExists($student)
    {
        $today = Carbon::now();

        if ($today->month == 8 && $today->day >= 24) {
            // من 24/8 لآخر 8 → يعتبر شهر 9
            $month = 9;
            $year = $today->year;
        } else {
            // عادي: الشهر الحالي
            $month = $today->month;
            $year = $today->year;
        }

        $group = $student->group;
        if (!$group) {
            return false;
        }

        $monthlyFee = $group->monthly_fee ?? 0;

        $exists = StudentFee::where('student_id', $student->id)
            ->where('month', $month)
            ->where('year', $year)
            ->exists();

        if (!$exists) {
            $discount = is_numeric($student->discount) ? min(max($student->discount, 0), 100) : 0;
            $discount_amount =  ($monthlyFee * ($discount  / 100));
            $final_amount = $monthlyFee - $discount_amount;

            StudentFee::create([
                'student_id' => $student->id,
                'group_id' => $group->id,
                'amount' => $monthlyFee,
                'status' => 'unpaid',
                'month' => $month,
                'year' => $year,
                'discount' => $discount_amount,
                'final_amount' => $final_amount,
                'date' => now()->toDateString(),
            ]);
            Log::info("Created fee for student {$student->id} for $month/$year");
        }

        return true;
    }
}
