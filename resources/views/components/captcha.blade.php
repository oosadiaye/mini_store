@php
    $captchaType = \App\Models\GlobalSetting::where('key', 'captcha_type')->first()?->value ?? 'none';
    $turnstileKey = \App\Models\GlobalSetting::where('key', 'turnstile_site_key')->first()?->value ?? config('services.turnstile.site_key');
    $recaptchaKey = \App\Models\GlobalSetting::where('key', 'recaptcha_site_key')->first()?->value ?? config('services.recaptcha.site_key');
@endphp

@if($captchaType === 'turnstile' && $turnstileKey)
    <div class="mt-4">
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        <div class="cf-turnstile" data-sitekey="{{ $turnstileKey }}"></div>
        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
    </div>
@elseif($captchaType === 'recaptcha' && $recaptchaKey)
    <div class="mt-4">
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaKey }}"></script>
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ $recaptchaKey }}', {action: 'submit'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        </script>
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
    </div>
@endif
