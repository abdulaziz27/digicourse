<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // membuat beberapa role
        // membuat default user untuk super admin

        $ownerRole = Role::create([
            'name' => 'owner'
        ]);

        $studentRole = Role::create([
            'name' => 'student'
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher'
        ]);

        // akun super admin untuk mengelola data awal
        // category, course, etc.
        $userOwner = User::create([
            'name' => 'Abdul Aziz',
            'occupation' => 'Programmer',
            'avatar' => 'images/default-avatar.png',
            'email' => 'aaziz@owner.com',
            'password' => bcrypt('123456')
        ]);

        $userOwner->assignRole($ownerRole);
    }
}
