<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class TurnstileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('The CAPTCHA field is required.');
            return;
        }

        $secret = \App\Models\GlobalSetting::where('key', 'turnstile_secret')->first()?->value 
                  ?? config('services.turnstile.secret');

        if (empty($secret)) {
            // If secret is not set, allow validation (dev mode or misconfiguration)
            // In production, this should always be set.
            return;
        }

        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->successful() || !$response->json('success')) {
            $fail('The CAPTCHA verification failed. Please try again.');
        }
    }
}
