<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'System Management',
                'slug' => 'system-management',
                'features' => [
                    [
                        'name' => 'Modules',
                        'slug' => 'modules',
                        'permissions' => [
                            ['name' => 'module_create'],
                            ['name' => 'module_list'],
                            ['name' => 'module_edit'],
                            ['name' => 'module_delete'],
                        ]
                    ],
                    [
                        'name' => 'Features',
                        'slug' => 'features',
                        'permissions' => [
                            ['name' => 'feature_create'],
                            ['name' => 'feature_list'],
                            ['name' => 'feature_edit'],
                            ['name' => 'feature_delete'],
                        ]
                    ],
                    [
                        'name' => 'Roles',
                        'slug' => 'roles',
                        'permissions' => [
                            ['name' => 'role_create'],
                            ['name' => 'role_list'],
                            ['name' => 'role_edit'],
                            ['name' => 'role_delete'],
                        ]
                    ],
                    [
                        'name' => 'Permissions',
                        'slug' => 'permissions',
                        'permissions' => [
                            ['name' => 'permission_create'],
                            ['name' => 'permission_list'],
                            ['name' => 'permission_edit'],
                            ['name' => 'permission_delete'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'HR Management',
                'slug' => 'hr-management',
                'features' => [
                    [
                        'name' => 'Attendance',
                        'slug' => 'attendance',
                        'permissions' => [
                            ['name' => 'attendance_create'],
                            ['name' => 'attendance_list'],
                            ['name' => 'attendance_edit'],
                            ['name' => 'attendance_delete'],
                        ]
                    ],
                    [
                        'name' => 'Payroll',
                        'slug' => 'payroll',
                        'permissions' => [
                            ['name' => 'payroll_create'],
                            ['name' => 'payroll_list'],
                            ['name' => 'payroll_edit'],
                            ['name' => 'payroll_delete'],
                        ]
                    ],
                    [
                        'name' => 'Paycheck',
                        'slug' => 'paycheck',
                        'permissions' => [
                            ['name' => 'paycheck_create'],
                            ['name' => 'paycheck_list'],
                            ['name' => 'paycheck_edit'],
                            ['name' => 'paycheck_delete'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Inventory Management',
                'slug' => 'inventory-management',
                'features' => [
                    [
                        'name' => 'Stock Request',
                        'slug' => 'stock-request',
                        'permissions' => [
                            ['name' => 'stock_request_create'],
                            ['name' => 'stock_request_list'],
                            ['name' => 'stock_request_edit'],
                            ['name' => 'stock_request_delete'],
                        ]
                    ],
                    [
                        'name' => 'Stock Transfer',
                        'slug' => 'stock-transfer',
                        'permissions' => [
                            ['name' => 'stock_transfer_create'],
                            ['name' => 'stock_transfer_list'],
                            ['name' => 'stock_transfer_edit'],
                            ['name' => 'stock_transfer_delete'],
                        ]
                    ],
                    [
                        'name' => 'Stock Adjustment',
                        'slug' => 'stock-adjustment',
                        'permissions' => [
                            ['name' => 'stock_adjustment_create'],
                            ['name' => 'stock_adjustment_list'],
                            ['name' => 'stock_adjustment_edit'],
                            ['name' => 'stock_adjustment_delete'],
                        ]
                    ],
                    [
                        'name' => 'Stock Report',
                        'slug' => 'stock-report',
                        'permissions' => [
                            ['name' => 'stock_report_list'],
                            ['name' => 'stock_report_download'],
                        ]
                    ]
                ]
            ]
        ];
        foreach ($modules as $m) {
            $module = Module::create([
                'name' => $m['name'],
                'slug' => $m['slug'],
            ]);
            
            foreach ($m['features'] as $f) { // Changed from $module['features'] to $m['features']
                $feature = Feature::create([
                    'name' => $f['name'],
                    'slug' => $f['slug'],
                    'module_id' => $module->id,
                ]);
                
                foreach ($f['permissions'] as $p) { // Changed from $feature['permissions'] to $f['permissions']
                    Permission::create([
                        'name' => $p['name'],
                        'feature_id' => $feature->id,
                    ]);
                }
            }
        }
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $systemRole = Role::where('code', 'system')->first();
            $systemRole->permissions()->attach($permission->id);
        }
        // admin permissions
        $adminFeatures = Feature::whereNotIn('slug', ['modules', 'roles', 'permissions', 'features'])->get();
        $adminRole = Role::where('code', 'admin')->first();
        foreach ($adminFeatures as $feature) {
            $adminRole->permissions()->attach($feature->permissions->pluck('id'));
        }
        // hr permissions
        $hrFeatures = Feature::whereIn('slug', ['attendance', 'payroll', 'paycheck'])->get();
        $hrManager = Role::where('code', 'hr_manager')->first();
        $hrAssistant = Role::where('code', 'hr_assistant')->first();
        foreach ($hrFeatures as $feature) {
            $hrManager->permissions()->attach($feature->permissions->pluck('id'));
            $hrAssistant->permissions()->attach($feature->permissions->pluck('id'));
        }
        // inventory permissions
        $inventoryFeatures = Feature::whereIn('slug', ['stock-request', 'stock-adjustment', 'stock-transfer', 'stock-report'])->get();
        $inventoryManager = Role::where('code', 'inventory_manager')->first();
        $inventoryAssistant = Role::where('code', 'inventory_assistant')->first();
        foreach ($inventoryFeatures as $feature) {
            $inventoryManager->permissions()->attach($feature->permissions->pluck('id'));
            $inventoryAssistant->permissions()->attach($feature->permissions->pluck('id'));
        }
    }
}