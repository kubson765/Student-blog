<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;

#[Signature('app:promote-to-moderator')]
#[Description('Command description')]

class PromoteToModerator extends Command
{
    protected $signature = 'user:promote-moderator {email} {--admin}';
    protected $description = 'Promuje użytkownika do roli moderatora (lub admina)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $role = $this->option('admin') ? 'admin' : 'moderator';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Użytkownik o emailu {$email} nie istnieje.");
            return self::FAILURE;
        }

        $user->update([
            'role' => $role,
            'moderator_since' => now(),
            'role_assigned_by' => auth()->id(), // może być null
        ]);

        $this->info("Użytkownik {$user->name} został promowany do roli: {$role}");

        return self::SUCCESS;
    }
}
