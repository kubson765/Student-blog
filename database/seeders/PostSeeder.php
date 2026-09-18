<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Realistyczne posty studenckie – 30 sztuk
     */
    private array $posts = [
        [
            'title' => 'Jak przetrwać pierwszą sesję egzaminacyjną – poradnik od starszego roku',
            'category' => 'Egzaminy i sesja',
            'tags' => ['Nauka', 'Organizacja czasu'],
            'status' => 'published',
        ],
        [
            'title' => 'Mój Erasmus w Lizbonie – 5 rzeczy, które chciałbym wiedzieć wcześniej',
            'category' => 'Kampus i wydarzenia',
            'tags' => ['Erasmus', 'Życie studenckie'],
            'status' => 'published',
        ],
        [
            'title' => 'Czy warto iść na studia informatyczne w 2026? Szczera opinia po 3 latach',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Informatyka', 'Kariera'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak zdobyć stypendium naukowe – kompletny przewodnik krok po kroku',
            'category' => 'Stypendia i pomoc',
            'tags' => ['Stypendium', 'Nauka'],
            'status' => 'published',
        ],
        [
            'title' => 'Życie w akademiku vs. wynajem mieszkania – porównanie kosztów',
            'category' => 'Życie studenckie',
            'tags' => ['Akademik', 'Życie studenckie'],
            'status' => 'published',
        ],
        [
            'title' => 'Praktyki w korpo – czego naprawdę się nauczyłem przez 3 miesiące',
            'category' => 'Praktyki i praca',
            'tags' => ['Praktyki', 'Praca'],
            'status' => 'published',
        ],
        [
            'title' => 'Psychologia na UJ – recenzja po pierwszym roku',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Psychologia', 'Wykłady'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak skutecznie robić notatki na wykładach – 5 metod, które testowałem',
            'category' => 'Wykłady i ćwiczenia',
            'tags' => ['Notatki', 'Nauka'],
            'status' => 'published',
        ],
        [
            'title' => 'Praca na studiach zaocznych – czy da się to pogodzić?',
            'category' => 'Praktyki i praca',
            'tags' => ['Praca', 'Organizacja czasu'],
            'status' => 'published',
        ],
        [
            'title' => 'Kolokwium z matematyki – jak się przygotować, żeby nie zwariować',
            'category' => 'Egzaminy i sesja',
            'tags' => ['Matematyka', 'Stres'],
            'status' => 'published',
        ],
        [
            'title' => 'Mój pierwszy projekt na studiach – co poszło nie tak i czego się nauczyłem',
            'category' => 'Wykłady i ćwiczenia',
            'tags' => ['Projekt', 'Informatyka'],
            'status' => 'published',
        ],
        [
            'title' => 'Koło naukowe – dlaczego warto, nawet jeśli nie jesteś orłem',
            'category' => 'Kampus i wydarzenia',
            'tags' => ['Koło naukowe', 'Motywacja'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak przygotować się do rozmowy o pracę będąc studentem',
            'category' => 'Kariera po studiach',
            'tags' => ['CV', 'Rozmowa kwalifikacyjna'],
            'status' => 'published',
        ],
        [
            'title' => 'Studia zaoczne vs. dzienne – moja perspektywa po zmianie trybu',
            'category' => 'Życie studenckie',
            'tags' => ['Organizacja czasu', 'Motywacja'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak radzić sobie ze stresem przed egzaminem – techniki, które działają',
            'category' => 'Egzaminy i sesja',
            'tags' => ['Stres', 'Motywacja'],
            'status' => 'published',
        ],
        [
            'title' => 'Wykładowca, który zmienił moje podejście do nauki',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Wykładowca', 'Motywacja'],
            'status' => 'published',
        ],
        [
            'title' => 'Budżet studenta – ile naprawdę kosztuje życie na studiach',
            'category' => 'Stypendia i pomoc',
            'tags' => ['Życie studenckie', 'Organizacja czasu'],
            'status' => 'published',
        ],
        [
            'title' => 'Prawo na UW – czy warto? Moja szczera opinia',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Prawo', 'Wykłady'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak wybrać temat pracy licencjackiej – praktyczny przewodnik',
            'category' => 'Poradniki',
            'tags' => ['Nauka', 'Motywacja'],
            'status' => 'published',
        ],
        [
            'title' => 'Erasmus+ – jak zdobyć stypendium na wyjazd zagraniczny',
            'category' => 'Stypendia i pomoc',
            'tags' => ['Erasmus', 'Stypendium'],
            'status' => 'published',
        ],
        [
            'title' => 'Prezentacja na zaliczenie – jak nie zwariować przed występem',
            'category' => 'Wykłady i ćwiczenia',
            'tags' => ['Prezentacja', 'Stres'],
            'status' => 'published',
        ],
        [
            'title' => 'Mechanika na PW – recenzja najtrudniejszego przedmiotu na roku',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Mechanika', 'Wykłady'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak pogodzić pracę z nauką – 7 praktycznych wskazówek',
            'category' => 'Praktyki i praca',
            'tags' => ['Praca', 'Organizacja czasu'],
            'status' => 'published',
        ],
        [
            'title' => 'Życie studenckie w Krakowie – najlepsze miejsca do nauki',
            'category' => 'Życie studenckie',
            'tags' => ['Życie studenckie', 'Kampus'],
            'status' => 'published',
        ],
        [
            'title' => 'Informatyka stosowana vs. czysta – którą wybrać?',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Informatyka', 'Kariera'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak zbudować portfolio na studiach – od czego zacząć',
            'category' => 'Kariera po studiach',
            'tags' => ['Kariera', 'CV'],
            'status' => 'published',
        ],
        [
            'title' => 'Sesja poprawkowa – jak się zmotywować po porażce',
            'category' => 'Egzaminy i sesja',
            'tags' => ['Motywacja', 'Stres'],
            'status' => 'published',
        ],
        [
            'title' => 'Medycyna – jak wygląda pierwszy rok na kierunku lekarskim',
            'category' => 'Recenzje przedmiotów',
            'tags' => ['Medycyna', 'Wykłady'],
            'status' => 'published',
        ],
        [
            'title' => 'Jak znaleźć staż w IT będąc na drugim roku',
            'category' => 'Praktyki i praca',
            'tags' => ['Staż', 'Informatyka'],
            'status' => 'published',
        ],
        [
            'title' => 'Samorząd studencki – czy warto się zaangażować?',
            'category' => 'Kampus i wydarzenia',
            'tags' => ['Samorząd studencki', 'Życie studenckie'],
            'status' => 'published',
        ],
    ];

    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $tags = Tag::all();

        if ($users->isEmpty()) {
            $this->command->error('❌ Brak użytkowników. Uruchom najpierw UserSeeder.');
            return;
        }

        foreach ($this->posts as $i => $postData) {
            $user = $users->random();
            $slug = Str::slug($postData['title']);

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $user->id,
                    'title' => $postData['title'],
                    'content' => $this->generateContent($postData['title'], $postData['category']),
                    'category' => $postData['category'],
                    'status' => $postData['status'],
                    'moderation_status' => 'approved',
                    'published_at' => now()->subDays(rand(1, 90)),
                ]
            );

            // Przypisz tagi zdefiniowane w poście + 0-2 losowe
            $definedTags = $tags->whereIn('name', $postData['tags'])->pluck('id')->toArray();
            $randomTags = $tags->random(rand(0, 2))->pluck('id')->toArray();
            $post->tags()->sync(array_unique(array_merge($definedTags, $randomTags)));
        }

        $this->command->info('✅ Utworzono ' . count($this->posts) . ' postów.');
    }

    /**
     * Generuj realistyczną treść posta
     */
    private function generateContent(string $title, string $category): string
    {
        $intro = [
            "Postanowiłem podzielić się swoim doświadczeniem, bo sam szukałem podobnych informacji, gdy zaczynałem. Mam nadzieję, że komuś to pomoże.",
            "Zbieram się do napisania tego od kilku tygodni. W końcu się udało. Oto moje przemyślenia.",
            "Zanim zacznę, chcę zaznaczyć, że to moja subiektywna perspektywa. Każdy ma inne doświadczenia.",
            "Od dawna chciałem to opisać. Może ktoś z Was był w podobnej sytuacji i podzieli się swoim doświadczeniem.",
        ];

        $paragraphs = [
            "Zacznijmy od początku. Kiedy zaczynałem studia, nie wiedziałem czego się spodziewać. Pierwsze tygodnie były trudne – nowe środowisko, nowi ludzie, nowe wymagania. Ale z czasem wszystko się ułożyło.",
            "Kluczowe okazało się podejście krok po kroku. Nie da się nauczyć wszystkiego naraz. Warto rozłożyć materiał na mniejsze części i pracować regularnie, zamiast zostawiać wszystko na ostatnią chwilę.",
            "Największym wyzwaniem była dla mnie organizacja czasu. Praca, studia, życie towarzyskie – wszystko wymagało uwagi. Pomogło mi prowadzenie kalendarza i planowanie tygodnia z wyprzedzeniem.",
            "Warto też pamiętać o odpoczynku. Studia to maraton, nie sprint. Regularne przerwy, sen i aktywność fizyczna są równie ważne jak nauka.",
            "Nie bać się prosić o pomoc. Starsze roki, prowadzący, koleżanki i koledzy z grupy – wszyscy mogą pomóc. Wstyd pytania to najgorszy doradca.",
            "Z czasem zauważyłem, że najwięcej uczę się przez praktykę. Teoria jest ważna, ale dopiero gdy coś zrobisz samodzielnie, rozumiesz to naprawdę.",
            "Chciałbym też wspomnieć o błędach. Każdy je popełnia. Ważne, żeby wyciągać wnioski i nie zniechęcać się. Porażka to nie koniec świata.",
            "Jeśli chodzi o materiał – warto korzystać z różnych źródeł. Nie tylko podręczniki, ale też filmy, kursy online, blogi. Czasem inne wyjaśnienie trafia lepiej.",
        ];

        $content = $paragraphs[array_rand($paragraphs)] . "\n\n";
        $content .= $paragraphs[array_rand($paragraphs)] . "\n\n";

        $sections = ['Moje doświadczenia', 'Praktyczne wskazówki', 'Czego się nauczyłem', 'Podsumowanie', 'Wnioski'];
        for ($i = 0; $i < rand(2, 4); $i++) {
            $content .= "## " . $sections[array_rand($sections)] . "\n\n";
            $content .= $paragraphs[array_rand($paragraphs)] . "\n\n";
            $content .= $paragraphs[array_rand($paragraphs)] . "\n\n";
        }

        $content .= "Mam nadzieję, że ten wpis był dla Was pomocny. Jeśli macie własne doświadczenia, chętnie poczytam w komentarzach!\n";

        return $content;
    }
}
