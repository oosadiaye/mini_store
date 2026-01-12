<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use App\Models\GlobalSetting;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('reCAPTCHA verification is required.');
            return;
        }

        // Try to get from GlobalSetting first, then fallback to config/env
        $secret = GlobalSetting::where('key', 'recaptcha_secret')->first()?->value 
                  ?? config('services.recaptcha.secret');

        if (empty($secret)) {
            // If secret is not set, allow validation (dev mode or misconfiguration)
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $responseData = $response->json();

        if (!$response->successful() || !($responseData['success'] ?? false)) {
            $fail('The reCAPTCHA verification failed. Please try again.');
            return;
        }

        // For v3, we should also check the score if we want to be strict
        // Threshold can be configured, default to 0.5
        $score = $responseData['score'] ?? 1.0;
        if ($score < 0.5) {
            $fail('Low confidence score from reCAPTCHA. Please try again or contact support.');
        }
    }
}
