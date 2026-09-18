<?php

namespace Database\Seeders;

use App\Models\AnonymousPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnonymousPostSeeder extends Seeder
{
    private array $posts = [
        [
            'title' => 'Nie wiem czy dam radę na tym kierunku – potrzebuję wsparcia',
            'category' => 'Życie studenckie',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Czy ktoś jeszcze czuje się oszukany przez system studiów?',
            'category' => 'Ogólne',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Jak sobie radzicie z samotnością na studiach zaocznych?',
            'category' => 'Życie studenckie',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Wykładowca publicznie mnie upokorzył – co zrobić?',
            'category' => 'Wykłady i ćwiczenia',
            'moderation_status' => 'pending',
        ],
        [
            'title' => 'Nie mogę znaleźć pracy na studiach – macie jakieś rady?',
            'category' => 'Praktyki i praca',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Mam dość sesji – jak wy to wytrzymujecie?',
            'category' => 'Egzaminy i sesja',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Czy warto zmieniać kierunek po pierwszym roku?',
            'category' => 'Ogólne',
            'moderation_status' => 'pending',
        ],
        [
            'title' => 'Czuję się samotny na studiach – jak sobie radzić?',
            'category' => 'Życie studenckie',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Jak przestać się stresować egzaminami?',
            'category' => 'Egzaminy i sesja',
            'moderation_status' => 'approved',
        ],
        [
            'title' => 'Czy ktoś z Was żałuje wyboru kierunku?',
            'category' => 'Ogólne',
            'moderation_status' => 'approved',
        ],
    ];

    public function run(): void
    {
        foreach ($this->posts as $i => $data) {
            $slug = Str::slug($data['title']);

            AnonymousPost::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'content' => $this->generateContent($data['title']),
                    'category' => $data['category'],
                    'status' => 'published',
                    'moderation_status' => $data['moderation_status'],
                    'fingerprint' => hash('sha256', 'seed-' . $i . config('app.key')),
                    'published_at' => $data['moderation_status'] === 'approved'
                        ? now()->subDays(rand(1, 40))
                        : null,
                ]
            );
        }

        $this->command->info('✅ Utworzono ' . count($this->posts) . ' anonimowych postów.');
    }

    private function generateContent(string $title): string
    {
        $intro = [
            "Piszę anonimowo, bo nie chcę, żeby ktoś mnie rozpoznał.",
            "Nie wiem, do kogo się zwrócić, więc piszę tutaj.",
            "Nie chcę, żeby to wyszło na moim roku, dlatego anonimowo.",
        ];

        return $intro[array_rand($intro)] . "\n\n" .
            "Od kilku tygodni zmagam się z tym problemem i nie wiem, jak sobie poradzić. " .
            "Próbowałem rozmawiać z bliskimi, ale nie do końca rozumieją, przez co przechodzę.\n\n" .
            "Czy ktoś z Was był w podobnej sytuacji? Jak sobie poradziliście? " .
            "Będę wdzięczny za każdą radę, nawet najmniejszą.\n\n" .
            "Z góry dziękuję za wsparcie.";
    }
}
