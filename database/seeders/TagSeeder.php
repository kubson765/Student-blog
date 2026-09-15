<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Kierunki studiów
            'Informatyka',
            'Prawo',
            'Psychologia',
            'Mechanika',
            'Medycyna',
            'Ekonomia',
            'Zarządzanie',
            'Matematyka',
            'Fizyka',
            'Filologia',

            // Życie studenckie
            'Życie studenckie',
            'Akademik',
            'Stypendium',
            'Erasmus',
            'Koło naukowe',
            'Samorząd studencki',

            // Akademia
            'Wykładowca',
            'Wykłady',
            'Ćwiczenia',
            'Laboratoria',
            'Projekt',
            'Prezentacja',
            'Egzamin',
            'Kolokwium',
            'Sesja',

            // Kariera
            'Praktyki',
            'Staż',
            'Praca',
            'CV',
            'Rozmowa kwalifikacyjna',

            // Nauka
            'Nauka',
            'Notatki',
            'Powtórki',
            'Organizacja czasu',
            'Stres',
            'Motywacja',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
        }
    }
}
