<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Panic Button System</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="app-body min-h-screen flex items-center justify-center p-4 font-sans">

    <main class="login-card w-full max-w-4xl grid md:grid-cols-2 ui-card overflow-hidden shadow-2xl shadow-blue-900/10 ring-1 ring-slate-900/5">
        {{-- Brand panel --}}
        <div class="relative overflow-hidden hidden md:flex flex-col justify-between p-10 text-white bg-gradient-to-br from-blue-700 via-blue-600 to-teal-600">
            <div aria-hidden="true" class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full border border-white/10"></div>
            <div aria-hidden="true" class="pointer-events-none absolute -left-20 -bottom-24 h-72 w-72 rounded-full bg-white/5"></div>
            <div class="relative flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white p-1.5 shadow-md">
                    <img src="/images/logo.png" alt="JHCSC Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-sm font-bold tracking-wide">JHCSC</span>
            </div>
            <div class="relative">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur mb-4">
                    <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75 animate-ping"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span></span>
                    Monitoring active
                </span>
                <h2 class="text-3xl font-extrabold leading-tight">Smart Panic Button<br>Emergency Response</h2>
                <p class="mt-3 text-sm text-blue-100 leading-relaxed">
                    Real-time campus alerts for DRRMO responders and the Clinic, from the button press to resolution.
                </p>
            </div>
            <p class="relative text-xs text-blue-100/80">&copy; {{ date('Y') }} JHCSC. All rights reserved.</p>
        </div>

        {{-- Form --}}
        <div class="p-8 sm:p-10 flex flex-col justify-center">
            <div class="md:hidden flex items-center gap-3 mb-6">
                <img src="/images/logo.png" alt="JHCSC Logo" class="w-12 h-12 object-contain">
                <span class="text-sm font-bold text-slate-700">Smart Panic Button System</span>
            </div>

            <h1 class="text-2xl font-extrabold text-brand-dark">Welcome back</h1>
            <p class="text-sm text-brand-text mt-1 mb-8">Sign in to the response dashboard.</p>

            @if ($errors->any())
                <div role="alert" class="flex gap-2 bg-red-50 border border-red-200 text-red-700 text-sm p-3 rounded-xl mb-6">
                    <svg class="h-5 w-5 shrink-0 mt-px" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-semibold text-brand-dark mb-1.5">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        autocomplete="username" class="ui-input">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-brand-dark mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            autocomplete="current-password" class="ui-input pr-12">
                        <button type="button" id="toggle-password" aria-label="Show password" aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-brand-blue rounded-r-xl">
                            <svg id="eye-on" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-off" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.9 17.9A10.1 10.1 0 0112 19c-6.5 0-10-7-10-7a18 18 0 014.1-5.1M9.9 5.2A9.7 9.7 0 0112 5c6.5 0 10 7 10 7a18 18 0 01-2.2 3.2M1 1l22 22"/></svg>
                        </button>
                    </div>
                    <p id="caps-warning" class="hidden mt-1.5 text-xs font-medium text-amber-600" role="status">Caps Lock is on.</p>
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 rounded border-brand-border text-brand-blue focus:ring-brand-blue">
                    <label for="remember" class="ml-2 block text-sm text-brand-text">Remember me</label>
                </div>

                <button type="submit" id="login-submit" class="ui-btn w-full disabled:opacity-70 disabled:cursor-not-allowed">
                    <svg id="login-spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".3" stroke-width="4"/><path d="M12 2a10 10 0 0110 10" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
                    <span id="login-label">Sign In</span>
                </button>
            </form>
        </div>
    </main>

    <script>
        (function () {
            var pw = document.getElementById('password');
            var toggle = document.getElementById('toggle-password');
            var caps = document.getElementById('caps-warning');
            var form = document.getElementById('login-form');

            toggle.addEventListener('click', function () {
                var show = pw.type === 'password';
                pw.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show);
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                document.getElementById('eye-on').classList.toggle('hidden', show);
                document.getElementById('eye-off').classList.toggle('hidden', !show);
            });

            function checkCaps(e) {
                if (e.getModifierState) caps.classList.toggle('hidden', !e.getModifierState('CapsLock'));
            }
            pw.addEventListener('keydown', checkCaps);
            pw.addEventListener('keyup', checkCaps);
            pw.addEventListener('blur', function () { caps.classList.add('hidden'); });

            form.addEventListener('submit', function () {
                var btn = document.getElementById('login-submit');
                btn.disabled = true;
                document.getElementById('login-spinner').classList.remove('hidden');
                document.getElementById('login-label').textContent = 'Signing in…';
            });
            // Re-enable when restored from bfcache (back button after submit)
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) {
                    document.getElementById('login-submit').disabled = false;
                    document.getElementById('login-spinner').classList.add('hidden');
                    document.getElementById('login-label').textContent = 'Sign In';
                }
            });
        })();
    </script>
</body>
</html>
