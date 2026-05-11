<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('123456789');

        // Owner — perfil administrador completo (dono da equipe)
        $owner = User::factory()->create([
            'name' => 'Proprietário',
            'email' => 'owner@atelier.com',
            'password' => $password,
        ]);

        $mainTeam = $owner->currentTeam;

        // Admin — perfil administrador (gerencia produtos e membros)
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@atelier.com',
            'password' => $password,
        ]);

        $mainTeam->members()->attach($admin, ['role' => TeamRole::Admin->value]);

        // Membro — perfil usuário (somente visualização)
        $member = User::factory()->create([
            'name' => 'Usuário',
            'email' => 'usuario@atelier.com',
            'password' => $password,
        ]);

        $mainTeam->members()->attach($member, ['role' => TeamRole::Member->value]);

        $this->command->info('Usuários de teste criados:');
        $this->command->table(
            ['Nome', 'E-mail', 'Perfil', 'Senha'],
            [
                [$owner->name, $owner->email, 'Owner (administrador)', '123456789'],
                [$admin->name, $admin->email, 'Admin (administrador)', '123456789'],
                [$member->name, $member->email, 'Member (usuário)', '123456789'],
            ],
        );
    }
}
