<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $this->call(PermissionSeeder::class);
        
        
        // $admin = User::updateOrCreate(
        //     ['email' => 'admin@gmail.com'],
        //     [
        //         'name' => 'Admin',
        //         'password' => Hash::make('admin123'),
        //     ]
        // );

        // // إنشاء أو تحديث الدور
        // $role = Role::firstOrCreate(['name' => 'super-admin']);

        // // ربط جميع الصلاحيات بالدور
        // $role->syncPermissions(Permission::all());

        // // ربط الدور باليوزر
        // if (! $admin->hasRole('super-admin')) {
        //     $admin->assignRole($role);
        // }
    }
}
