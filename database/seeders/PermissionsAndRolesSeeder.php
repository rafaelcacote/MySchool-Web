<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Guard padrão usado pela aplicação (Fortify/Inertia)
        $guard = 'web';

        // Permissões de Escolas (Tenants)
        $escolasPermissions = [
            'escolas.visualizar',
            'escolas.criar',
            'escolas.editar',
            'escolas.excluir',
        ];

        // Permissões de Usuários
        $usuariosPermissions = [
            'usuarios.visualizar',
            'usuarios.criar',
            'usuarios.editar',
            'usuarios.excluir',
        ];

        // Permissões de Roles
        $rolesPermissions = [
            'roles.visualizar',
            'roles.criar',
            'roles.editar',
            'roles.excluir',
        ];

        // Permissões de Permissões
        $permissionsPermissions = [
            'permissoes.visualizar',
            'permissoes.criar',
            'permissoes.editar',
            'permissoes.excluir',
        ];

        // Permissões de Assinaturas
        $assinaturasPermissions = [
            'assinaturas.visualizar',
        ];

        // Permissões de Planos
        $planosPermissions = [
            'planos.visualizar',
            'planos.criar',
            'planos.editar',
        ];

        // Permissões de Logs do Sistema
        $logsPermissions = [
            'logs.visualizar',
        ];

        // Todas as permissões
        $allPermissions = array_merge(
            $escolasPermissions,
            $usuariosPermissions,
            $rolesPermissions,
            $permissionsPermissions,
            $assinaturasPermissions,
            $planosPermissions,
            $logsPermissions
        );

        // Criar todas as permissões
        foreach ($allPermissions as $permissionName) {
            Permission::findOrCreate($permissionName, $guard);
        }

        // Perfil Administrador Geral
        $role = Role::findOrCreate('Administrador Geral', $guard);
        $role->syncPermissions($allPermissions);
    }
}


