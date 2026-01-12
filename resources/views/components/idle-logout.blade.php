@props([
    'logoutRoute',
    'timeout' => 900 // Default to 15 minutes (900 seconds)
])

<div x-data="idleLogout({
    logoutRoute: '{{ $logoutRoute }}',
    timeout: {{ $timeout }}
})" x-init="init()" @mousemove.window="resetTimer()" @keydown.window="resetTimer()" @click.window="resetTimer()" @scroll.window="resetTimer()">
</div>

<script>
    function idleLogout(config) {
        return {
            logoutRoute: config.logoutRoute,
            timeout: config.timeout * 1000,
            idleTimer: null,

            init() {
                this.resetTimer();
                console.log('Idle logout initialized with ' + config.timeout + ' seconds timeout');
            },

            resetTimer() {
                clearTimeout(this.idleTimer);
                this.idleTimer = setTimeout(() => this.logout(), this.timeout);
            },

            logout() {
                console.log('User idle for too long. Logging out...');
                
                // If there's a logout form on the page, find and submit it
                const logoutForm = document.querySelector('form[action="' + this.logoutRoute + '"]');
                if (logoutForm) {
                    logoutForm.submit();
                    return;
                }

                // If no form found, try to find ANY logout form or use a POST request
                const anyLogoutForm = document.querySelector('form#logout-form') || document.querySelector('form[id*="logout"]');
                if (anyLogoutForm) {
                    anyLogoutForm.submit();
                    return;
                }

                // Fallback: Create a hidden form and submit it (standard Laravel logout requires POST)
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.logoutRoute;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script>
