<?php

namespace Database\Seeders;

use App\Models\AnonymousPost;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ Brak użytkowników.');
            return;
        }

        $reports = [
            ['reason' => 'spam', 'description' => 'Wygląda jak reklama'],
            ['reason' => 'misinformation', 'description' => 'Nieprawdziwe informacje'],
            ['reason' => 'harassment', 'description' => 'Atak personalny'],
            ['reason' => 'spam', 'description' => 'Powtarzające się treści'],
            ['reason' => 'other', 'description' => 'Nie pasuje do tematu'],
        ];

        $count = 0;

        foreach ($reports as $i => $data) {
            $reportable = match ($i % 3) {
                0 => Post::inRandomOrder()->first(),
                1 => Comment::inRandomOrder()->first(),
                2 => AnonymousPost::inRandomOrder()->first(),
            };

            if (!$reportable) continue;

            try {
                Report::create([
                    'reporter_id' => $users->random()->id,
                    'reportable_type' => get_class($reportable),
                    'reportable_id' => $reportable->id,
                    'reason' => $data['reason'],
                    'description' => $data['description'],
                    'status' => 'pending',
                    'reporter_fingerprint' => hash('sha256', 'seed-report-' . $i),
                ]);
                $count++;
            } catch (\Exception $e) {
                // Duplikat – pomiń
            }
        }

        $this->command->info("✅ Utworzono {$count} zgłoszeń.");
    }
}
