<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Interaction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->error('❌ Potrzeba minimum 2 użytkowników.');
            return;
        }

        // ============================================
        // GŁOSY NA POSTY
        // ============================================
        $posts = Post::where('status', 'published')->get();
        $postVotes = 0;

        foreach ($posts as $post) {
            // Losowa liczba głosujących (3-10 lub mniej niż userów)
            $voterCount = rand(3, min(10, $users->count() - 1));

            // Wybierz różnych użytkowników, poza autorem
            $voters = $users
                ->where('id', '!=', $post->user_id)
                ->random($voterCount);

            foreach ($voters as $voter) {
                // 75% szans na upvote, 25% na downvote
                $type = rand(1, 100) <= 75 ? 'upvote' : 'downvote';

                try {
                    Interaction::create([
                        'user_id' => $voter->id,
                        'interactable_type' => Post::class,
                        'interactable_id' => $post->id,
                        'type' => $type,
                        'value' => $type === 'upvote' ? 1 : -1,
                        'created_at' => $post->created_at->addDays(rand(0, 5)),
                    ]);
                    $postVotes++;
                } catch (\Exception $e) {
                    // Unikalny indeks – pomiń duplikaty
                }
            }
        }

        // ============================================
        // GŁOSY NA KOMENTARZE
        // ============================================
        $comments = Comment::all();
        $commentVotes = 0;

        foreach ($comments as $comment) {
            // Nie każdy komentarz ma głosy
            if (rand(1, 100) > 60) continue;  // 60% szans na głosy

            $voterCount = rand(1, min(5, $users->count() - 1));

            $voters = $users
                ->where('id', '!=', $comment->user_id)
                ->random($voterCount);

            foreach ($voters as $voter) {
                $type = rand(1, 100) <= 85 ? 'upvote' : 'downvote';

                try {
                    Interaction::create([
                        'user_id' => $voter->id,
                        'interactable_type' => Comment::class,
                        'interactable_id' => $comment->id,
                        'type' => $type,
                        'value' => $type === 'upvote' ? 1 : -1,
                        'created_at' => $comment->created_at->addHours(rand(1, 48)),
                    ]);
                    $commentVotes++;
                } catch (\Exception $e) {
                    // Duplikat – pomiń
                }
            }
        }

        $this->command->info("✅ Utworzono {$postVotes} głosów na posty i {$commentVotes} na komentarze.");
    }
}
