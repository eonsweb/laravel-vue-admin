<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //Create permissions
        $resources = [
            'users'
        ];

        $actions = [
            'view',
            'show',
            'create',
            'update',
            'delete',
        ];

        //create permissions
        foreach($resources as $resource)
        {
            foreach($actions as $action)
            {
                Permission::firstOrCreate([
                    'name' => "{$action}_{$resource}",
                    "guard_name" => "api"
                ]);
            }
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'api'
        ]);

        //Assign all Permissions to Admin
        $adminRole->syncPermissions(Permission::all());

        $userPermissions = Permission::whereIn('name',[
            'show_users',
            'update_users'
        ])->get();

        //Assing roles to User
        $userRole->syncPermissions($userPermissions);
    }
}
