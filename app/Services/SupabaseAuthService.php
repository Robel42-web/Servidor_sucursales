<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class SupabaseAuthService
{
    public function login(string $email, string $password): Response
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(15)
            ->withHeaders([
                'apikey' => config('services.supabase.publishable_key'),
            ])
            ->post(
                rtrim(config('services.supabase.url'), '/')
                    . '/auth/v1/token?grant_type=password',
                [
                    'email' => $email,
                    'password' => $password,
                ]
            );
    }
}
