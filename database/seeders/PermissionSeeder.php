<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions=[
            [
                "name" => "view groups",
                "name_ar" => "عرض المجموعات",
                "guard_name" => "web",
                'section' => 'groups',
                "created_at" => now(),
            ],
            [
                "name" => "create groups",
                "name_ar" => "إنشاء مجموعات",
                "guard_name" => "web",
                'section' => 'groups',
                "created_at" => now(),
            ],
            [
                "name" => "edit groups",
                "name_ar" => "تعديل المجموعات",
                "guard_name" => "web",
                'section' => 'groups',
                "created_at" => now(),
            ],
            [
                "name" => "attendance students",
                "name_ar" => "حضور الطلاب",
                "guard_name" => "web",
                'section' => 'groups',
                "created_at" => now(),
            ],
            [
                "name"=>"view students of group",
                "name_ar" => "عرض طلاب المجموعة",
                "guard_name" => "web",
                'section' => 'groups',
                "created_at" => now(),

            ],
            [
                "name"=>"download students excel sheet of group",
                "name_ar" => 'تحميل ملف إكسل لطلاب المجموعة',
                "guard_name" => "web",  
                "section" => 'groups',
                "created_at" => now(),
            ],
            
            [
                "name" => "view students",
                "name_ar" => "عرض الطلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name" => "create students",
                "name_ar" => "إنشاء طلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name" => "edit students",
                "name_ar" => "تعديل الطلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name" => "delete students",
                "name_ar" => "حذف الطلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name"=>"block students",
                "name_ar" => "حظر الطلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name"=>"view blocked students",
                "name_ar" => "عرض الطلاب المحظورين",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name"=>"view student profile",
                "name_ar" => "عرض ملف الطالب الشخصي",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name"=>"add payment for student",
                "name_ar" => "إضافة دفعة للطالب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            [
                "name"=>"download students excel sheet",
                "name_ar" => "تحميل ملف إكسل للطلاب",
                "guard_name" => "web",
                'section' => 'students',
                "created_at" => now(),
            ],
            
            [
                "name"=>"view exams",
                "name_ar" => "عرض الامتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"create exams",
                "name_ar" => "إنشاء امتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"edit exams",
                "name_ar" => "تعديل الامتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"delete exams",
                "name_ar" => "حذف الامتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"view exam results",
                "name_ar" => "عرض نتائج الامتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"add scores for students",
                "name_ar" => "إضافة درجات للطلاب",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                "name"=>"download exam results",
                "name_ar" => "تحميل نتائج الامتحانات",
                "guard_name" => "web",
                'section' => 'exams',
                "created_at" => now(),
            ],
            [
                'name'=>"view financial reports",
                "name_ar" => "عرض التقارير المالية",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),
            ],
            [
                'name'=>'download financial reports',
                "name_ar" => "تحميل التقارير المالية",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),
            ],
            [
                "name"=>'view attendance reports',
                "name_ar" => "عرض تقارير الحضور",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),
            ],
            [
                'name'=>"download attendance reports", 
                'name_ar' => "تحميل تقارير الحضور",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),

            ],
            [
                'name'=>'view exam reports',
                "name_ar" => "عرض تقارير الامتحانات",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),
            ],
            ['name'=>'download exam reports',
                "name_ar" => "تحميل تقارير الامتحانات",
                "guard_name" => "web",
                'section' => 'reports',
                "created_at" => now(),
            ],
            
            [
                'name'=>"view dashboard",
                "name_ar" => "عرض لوحة التحكم",
                "guard_name" => "web",
                'section' => 'dashboard',
                "created_at" => now(),
            ],

            [
                'name'=>"view roles",
                "name_ar" => "عرض الأدوار",
                "guard_name" => "web",
                'section' => 'roles',
                "created_at" => now(),
            ],
            [
                'name'=>"create roles",
                "name_ar" => "إنشاء أدوار",
                "guard_name" => "web",
                'section' => 'roles',
                "created_at" => now(),
            ],
            [
                'name'=>"edit roles",
                "name_ar" => "تعديل الأدوار",
                "guard_name" => "web",
                'section' => 'roles',
                "created_at" => now(),
            ],
            [
                'name'=>"delete roles",
                "name_ar" => "حذف الأدوار",
                "guard_name" => "web",
                'section' => 'roles',
                "created_at" => now(),
            ],
            
            [
                'name'=>'view users',
                "name_ar" => "عرض المستخدمين",
                "guard_name" => "web",
                'section' => 'users',
                "created_at" => now(),
            ],
            [
                'name'=>'create users',
                "name_ar" => "إنشاء مستخدمين",
                "guard_name" => "web",
                'section' => 'users',
                "created_at" => now(),
            ],
            [
                'name'=>'edit users',
                "name_ar" => "تعديل المستخدمين",
                "guard_name" => "web",
                'section' => 'users',
                "created_at" => now(),
            ],
            [
                'name'=>'delete users',
                "name_ar" => "حذف المستخدمين",
                "guard_name" => "web",
                'section' => 'users',
                "created_at" => now(),
            ],

        ];
        DB::table('permissions')->insert($permissions);
    }
}
