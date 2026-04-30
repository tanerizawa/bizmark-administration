<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminResetPassword extends Command
{
    protected $signature = 'admin:reset-password
                            {username? : Username atau email user yang akan di-reset}
                            {--password= : Password baru (akan di-prompt kalau kosong)}
                            {--random : Generate password acak 20 karakter dan print ke stdout}';

    protected $description = 'Reset password user admin. Wajib dipakai setelah kredensial bocor (mis. .login-access).';

    public function handle(): int
    {
        $username = $this->argument('username') ?? $this->ask('Username atau email user');

        if (! $username) {
            $this->error('Username wajib diisi.');

            return self::INVALID;
        }

        $user = User::query()
            ->where('email', $username)
            ->orWhere('username', $username)
            ->first();

        if (! $user) {
            $this->error("User '{$username}' tidak ditemukan.");

            return self::FAILURE;
        }

        $this->info("User ditemukan: {$user->name} <{$user->email}>");

        if ($this->option('random')) {
            $password = $this->generateStrongPassword();
            $this->line('');
            $this->warn('=== CATAT PASSWORD INI (hanya ditampilkan sekali) ===');
            $this->line($password);
            $this->warn('===================================================');
            $this->line('');
        } else {
            $password = $this->option('password') ?: $this->secret('Password baru');
            $confirm = $this->option('password') ?: $this->secret('Konfirmasi password baru');

            if (! $this->option('password') && $password !== $confirm) {
                $this->error('Konfirmasi password tidak cocok.');

                return self::INVALID;
            }
        }

        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', Password::min(10)->letters()->numbers()->symbols()]]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $err) {
                $this->error($err);
            }

            return self::INVALID;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password untuk '{$user->email}' berhasil di-reset.");
        $this->line('Rekomendasi: logout semua sesi aktif user ini jika perlu.');

        return self::SUCCESS;
    }

    private function generateStrongPassword(int $length = 20): string
    {
        $sets = [
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '0123456789',
            '!@#$%^&*()-_=+[]{};:,.?',
        ];

        $password = '';
        foreach ($sets as $set) {
            $password .= $set[random_int(0, strlen($set) - 1)];
        }

        $all = implode('', $sets);
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }
}
