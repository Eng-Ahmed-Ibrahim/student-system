<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Student;
use Milon\Barcode\DNS1D; // تأكد إن الباكيج موجود
class GroupService
{
    public function update_code_of_students()
    {

        $groups = Group::all();

        foreach ($groups as $group) {
            // جلب كل الطلاب في المجموعة مرتبين حسب created_at
            $students = Student::where('group_id', $group->id)
                ->orderBy('created_at', 'asc')
                ->get();

            // توليد barcode
            $barcode = new DNS1D();
            $barcode->setStorPath(__DIR__ . "/cache/");

            foreach ($students as $index => $student) {
                // إنشاء كود الطالب الجديد
                $student_code = intval($group->code) + $index + 1;

                $barcodeImage = $barcode->getBarcodePNG($student_code, 'C39');

                // تحديث الطالب
                $student->update([
                    'student_code' => $student_code,
                    'barcode' => $barcodeImage,
                ]);
            }
        }
    }
}
