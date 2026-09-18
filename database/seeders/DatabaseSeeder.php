<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TagSeeder::class,           // ← masz już
            PostSeeder::class,
            AnonymousPostSeeder::class,
            CommentSeeder::class,
            VoteSeeder::class,
            ReportSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Baza gotowa do prezentacji!');
        $this->command->info('');
        $this->command->info('📧 Konta do testowania:');
        $this->command->info('   Admin:     admin@myblog.test / password');
        $this->command->info('   Moderator: piotr@myblog.test / password');
        $this->command->info('   User:      michal@myblog.test / password');
    }
}
