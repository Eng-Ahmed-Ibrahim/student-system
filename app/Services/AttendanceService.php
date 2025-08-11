<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\StudentFee;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    public function generateAttendanceIfNotExists()
    {
        // $today = now()->toDateString();

        // $alreadyGenerated = Attendance::whereDate('date', $today)->exists();

        // if (!$alreadyGenerated) {
        $this->generateDailyAttendance();
        // }
        return true;
    }
    private function generateDailyAttendance()
    {
        $today = now()->format('l'); // مثل "Saturday"
        $todayDate = now()->toDateString();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $groups = Group::whereJsonContains('days', $today)->with('students')->get();

        foreach ($groups as $group) {
            $studentIds = $group->students->pluck('id')->toArray();

            // اجلب كل الحضور الموجودين مسبقًا لهؤلاء الطلاب في هذا اليوم والمجموعة
            /** @var \App\Models\Group $group */
            $existingAttendances = Attendance::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('date', $todayDate)
                ->pluck('student_id')
                ->toArray();

            // فلترة الطلاب اللي لم يتم تسجيل حضورهم
            $newStudents = $group->students->whereNotIn('id', $existingAttendances);

            $insertData = [];
            foreach ($newStudents as $student) {
                /** @var \App\Models\Student $student */
                $insertData[] = [
                    'student_id' => $student->id,
                    'group_id' => $group->id,
                    'date' => $todayDate,
                    'year' => $currentYear,
                    'month' => $currentMonth,
                    'status' => false,
                    'class_start_at' => $group->time,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إدخال دفعة واحدة
            if (!empty($insertData)) {
                Attendance::insert($insertData);
            }
        }
    }

    public function changeStatusOfAttendance($studentId, $status)
    {
        $check = Attendance::where("student_id", $studentId)->where('date', Carbon::today()->toDateString())->first();
        if($check->status == $status)
            return true;
        if (! $check)
            return false;
        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'status' => $status,
                'time' => $status == 0 ? null : now()->format('H:i:s'),
            ]
        );
        if ($attendance->status)
            $this->add_fees($attendance->id, $studentId);
        else
            $this->delete_fees($attendance->id, $studentId);

        return $attendance;
    }
    public function delete_fees($attendance_id, $studentId)
    {
        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;
        $date = Carbon::today()->toDateString();



        $fee = StudentFee::where('student_id', $studentId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('date', $date)
            ->where('attendance_id', $attendance_id)
            ->first();
        if ($fee)
            $fee->delete();
    }

    public function add_fees($attendance_id, $studentId)
    {
        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;
        $date = Carbon::today()->toDateString();

        $student = Student::with('group')->findOrFail($studentId);
        $group = $student->group;
        if (!$group) {
            return false;
        }

        $monthlyFee = $group->monthly_fee ?? 0;

        $exists = StudentFee::where('student_id', $student->id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('date', $date)
            ->where('attendance_id', $attendance_id)
            ->exists();


        if (!$exists) {
            $discount = is_numeric($student->discount) ? min(max($student->discount, 0), 100) : 0;

            $final_amount = $monthlyFee -  ($monthlyFee * ($discount  / 100));
            StudentFee::create([
                'student_id' => $student->id,
                // @phpstan-ignore-next-line
                'group_id' => $group->id,
                'amount' => $monthlyFee,
                'status' => 'unpaid',
                'month' => $month,
                'year' => $year,
                'date' => $date,
                'discount' => $discount,
                "attendance_id" => $attendance_id,
                'final_amount' => $final_amount,
            ]);
            Log::info("Created fee for student {$student->id} for $month/$year");
        }

        return true;
    }
}
