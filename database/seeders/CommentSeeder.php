<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    private array $comments = [
        'Świetny wpis! Też przez to przechodziłem i potwierdzam.',
        'Dzięki za podzielenie się doświadczeniem, bardzo pomocne.',
        'A jak było u Ciebie z pierwszym kolokwium?',
        'Ciekawy punkt widzenia, nie pomyślałem o tym w ten sposób.',
        'Mam inne doświadczenia, ale szanuję Twoją perspektywę.',
        'Czy mógłbyś rozwinąć temat organizacji czasu?',
        'U mnie to wyglądało zupełnie inaczej, ale każdy ma swoją drogę.',
        'Polecam też poczytać o technikach Pomodoro – mi bardzo pomogły.',
        'Bardzo motywujący wpis, dzięki!',
        'Kiedyś też tak myślałem, teraz wiem, że warto próbować.',
        'Zapisuję sobie, wrócę do tego przed sesją.',
        'Jakie książki polecasz na ten temat?',
        'Mam pytanie – ile czasu zajęło Ci dojście do tego poziomu?',
        'Dokładnie to samo słyszałem od starszych kolegów.',
        'Super, że dzielisz się takimi rzeczami!',
        'Zgadzam się w 100%.',
        'Ciekawe, muszę to przemyśleć.',
        'A czy próbowałeś innych metod?',
        'Bardzo dobre rady, na pewno skorzystam.',
        'To zależy od kierunku, u mnie było inaczej.',
    ];

    public function run(): void
    {
        $posts = Post::where('status', 'published')
            ->where('moderation_status', 'approved')
            ->get();
        $users = User::all();

        if ($posts->isEmpty() || $users->isEmpty()) {
            $this->command->error('❌ Brak postów lub użytkowników.');
            return;
        }

        $total = 0;

        foreach ($posts as $post) {
            // 3-8 głównych komentarzy
            $rootComments = collect();
            for ($i = 0; $i < rand(3, 8); $i++) {
                $rootComments->push(Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                    'parent_id' => null,
                    'content' => $this->comments[array_rand($this->comments)],
                    'moderation_status' => 'approved',
                    'created_at' => $post->created_at->addHours(rand(1, 72)),
                ]));
                $total++;
            }

            // 0-2 odpowiedzi na każdy główny komentarz
            foreach ($rootComments as $root) {
                if (rand(0, 10) <= 3) {  // 30% szans
                    $replyCount = rand(1, 2);
                    for ($j = 0; $j < $replyCount; $j++) {
                        Comment::create([
                            'post_id' => $post->id,
                            'user_id' => $users->random()->id,
                            'parent_id' => $root->id,
                            'content' => $this->comments[array_rand($this->comments)],
                            'moderation_status' => 'approved',
                            'created_at' => $root->created_at->addHours(rand(1, 24)),
                        ]);
                        $total++;
                    }
                }
            }
        }

        $this->command->info("✅ Utworzono {$total} komentarzy.");
    }
}
