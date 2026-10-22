<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class RecaptchaService
{
    public function verify(string $token): bool
    {
        $secretKey = config('services.recaptcha.secret_key');
        $domain = config('app.domain');

        if (!$secretKey) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                ]);

            if (!$response->successful()) {
                return false;
            }

            $result = $response->json();

            return ($result['success'] ?? false) === true
                && ($result['action'] ?? null) === 'create_comment'
                && ($result['score'] ?? 0) >= 0.5
                && ($result['hostname'] ?? null) === $domain;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
