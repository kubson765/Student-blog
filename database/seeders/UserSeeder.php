<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // ADMIN
        // ============================================
        User::updateOrCreate(
            ['email' => 'admin@myblog.test'],
            [
                'name' => 'Anna Kowalska',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // ============================================
        // MODERATORZY
        // ============================================
        $moderators = [
            ['name' => 'Piotr Nowak', 'email' => 'piotr@myblog.test'],
            ['name' => 'Katarzyna Wiśniewska', 'email' => 'katarzyna@myblog.test'],
        ];

        foreach ($moderators as $mod) {
            User::updateOrCreate(
                ['email' => $mod['email']],
                [
                    'name' => $mod['name'],
                    'password' => Hash::make('password'),
                    'role' => 'moderator',
                    'email_verified_at' => now(),
                ]
            );
        }

        // ============================================
        // ZWYKLI UŻYTKOWNICY (studenci)
        // ============================================
        $users = [
            ['name' => 'Michał Lewandowski', 'email' => 'michal@myblog.test'],
            ['name' => 'Aleksandra Dąbrowska', 'email' => 'ola@myblog.test'],
            ['name' => 'Tomasz Zieliński', 'email' => 'tomasz@myblog.test'],
            ['name' => 'Magdalena Szymańska', 'email' => 'magda@myblog.test'],
            ['name' => 'Jakub Wójcik', 'email' => 'jakub@myblog.test'],
            ['name' => 'Natalia Kaczmarek', 'email' => 'natalia@myblog.test'],
            ['name' => 'Marcin Krawczyk', 'email' => 'marcin@myblog.test'],
            ['name' => 'Weronika Piotrowska', 'email' => 'weronika@myblog.test'],
            ['name' => 'Bartosz Grabowski', 'email' => 'bartek@myblog.test'],
            ['name' => 'Julia Pawlak', 'email' => 'julia@myblog.test'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Utworzono ' . (2 + count($moderators) + count($users)) . ' użytkowników.');
        $this->command->info('   Admin: admin@myblog.test / password');
    }
}
