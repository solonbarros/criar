<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seed initial roles, permissions, and the default administrator user.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'visitors.manage',
            'access.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'Administrador' => $permissions,
            'Recepcao' => ['visitors.manage', 'access.manage'],
            'Auditoria' => ['reports.view'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($perms);
        }

        $agency = Agency::firstOrCreate([
            'name' => 'Secretaria Municipal de Inovação',
        ], [
            'cnpj' => '00.000.000/0001-00',
            'address' => 'Praça Central, 100',
            'contact_email' => 'contato@orgao.gov.br',
        ]);

        $department = Department::firstOrCreate([
            'name' => 'Administração Geral',
            'agency_id' => $agency->id,
        ], [
            'floor' => '1',
            'room' => '101',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'admin@admin'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin'),
                'department_id' => $department->id,
                'consent_at' => now(),
            ]
        );

        $user->assignRole('Administrador');
    }
}
