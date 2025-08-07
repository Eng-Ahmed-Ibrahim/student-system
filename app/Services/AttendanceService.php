<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Attendance;

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
            $existingAttendances = Attendance::whereIn('student_id', $studentIds)
                ->where('group_id', $group->id)
                ->where('date', $todayDate)
                ->pluck('student_id')
                ->toArray();

            // فلترة الطلاب اللي لم يتم تسجيل حضورهم
            $newStudents = $group->students->whereNotIn('id', $existingAttendances);

            $insertData = [];

            foreach ($newStudents as $student) {
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
        $check = Attendance::where("student_id", $studentId)->where('date', Carbon::today()->toDateString())->exists();
        if (! $check)
            return false;
        return Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'status' => $status,
                'time' => $status == 0 ? null : now()->format('H:i:s'),
            ]
        );
    }
    public function Check_if_all_student_attendance($group) {}
}
