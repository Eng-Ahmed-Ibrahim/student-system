<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Attendance;

class AttendanceService
{
    public function generateAttendanceIfNotExists()
    {
        $today = now()->toDateString();

        $alreadyGenerated = Attendance::whereDate('date', $today)->exists();

        if (!$alreadyGenerated) {
            $this->generateDailyAttendance();
        }
        return false;
    }
    private function generateDailyAttendance()
    {


        $today = now()->format('l'); // اسم اليوم مثل "Saturday"
        $todayDate = now()->toDateString();

        $groups = Group::whereJsonContains('days', $today)->with('students')->get();

        foreach ($groups as $group) {
            /** @var \App\Models\Student $student */
            foreach ($group->students as $student) {
                // نستخدم firstOrCreate لتفادي التكرار
                Attendance::firstOrCreate([
                    'student_id' => $student->id,
                    'date' => $todayDate,
                ], [
                    'status' => false,
                    'class_start_at' => $group->time,

                ]);
            }
        }
    }
    public function changeStatusOfAttendance($studentId , $status)
    {
        $check=Attendance::where("student_id",$studentId)->where('date',Carbon::today()->toDateString())->exists();
        if(! $check )
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
    
}
