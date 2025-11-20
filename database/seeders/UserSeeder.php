<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                "name" => "System Department",
                "code" => "system",
            ],
            [
                "name" => "Admin Department",
                "code" => "admin",
            ],
            [
                "name" => "HR Department",
                "code" => "hr",
            ],
            [
                "name" => "Finance Department",
                "code" => "finance",
            ],
            [
                "name" => "Marketing Department",
                "code" => "marketing",
            ],
            [
                "name" => "Inventory Department",
                "code" => "inventory",
            ]
        ];
        $users = [
            [
                'name' => 'Mr System',
                'email' => 'system@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mr Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mr HR',
                'email' => 'hr@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mr Finance',
                'email' => 'finance@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mr Marketing',
                'email' => 'marketing@gmail.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Mr Inventory',
                'email' => 'inventory@gmail.com',
                'password' => Hash::make('password'),
            ]
        ];
        $roles = [
            ['name' => 'System', 'code' => 'system', 'department_id' => 1],
            ['name' => 'Admin', 'code' => 'admin', 'department_id' => 2],
            ['name' => 'HR Manager', 'code' => 'hr_manager', 'department_id' => 3],
            ['name' => 'HR Assistant', 'code' => 'hr_assistant', 'department_id' => 3],
            ['name' => 'Inventory Manager', 'code' => 'inventory_manager', 'department_id' => 6],
            ['name' => 'Inventory Assistant', 'code' => 'inventory_assistant', 'department_id' => 6],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
        foreach ($users as $user) {
            User::create($user);
        }
        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'], 
                'code' => $role['code'], 
                'department_id' => $role['department_id']
            ]);
        }
        // Assign roles to users
        $roleUserAssignments = [
            ['user_id' => 1, 'role_id' => 1], // Mr System -> System
            ['user_id' => 2, 'role_id' => 2], // Mr Admin -> Admin
            ['user_id' => 3, 'role_id' => 3], // Mr HR -> HR Manager
            ['user_id' => 6, 'role_id' => 5], // Mr Inventory -> Inventory Manager
        ];

        User::find(1)->roles()->attach(1);
        User::find(2)->roles()->attach(2);
        User::find(3)->roles()->attach(3);
        User::find(6)->roles()->attach(5);
    }
}
