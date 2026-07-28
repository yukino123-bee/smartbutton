<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Panic Button System</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            blue: '#3B82F6',
                            red: '#EF4444',
                            orange: '#F59E0B',
                            green: '#15803D',
                            text: '#64748B',
                            dark: '#0F172A',
                            hover: '#F1F5F9'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-bg text-brand-dark font-sans h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-brand-card rounded-2xl shadow-xl border border-brand-border overflow-hidden">
        <div class="p-8 text-center border-b border-brand-border">
            <img src="/images/logo.png" alt="JHCSC Logo" class="w-16 h-16 object-contain mx-auto mb-4">
            <h1 class="text-2xl font-bold text-brand-dark mb-2">System Login</h1>
            <p class="text-xs text-brand-text">Smart Student Panic Button & Emergency Response System</p>
        </div>
        
        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-3 rounded-lg mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-brand-dark mb-2">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-3 text-brand-dark focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors shadow-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-brand-dark mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-3 text-brand-dark focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors shadow-sm">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-brand-border bg-brand-bg text-brand-blue focus:ring-brand-blue">
                        <label for="remember" class="ml-2 block text-sm text-brand-text">Remember me</label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-brand-blue hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-blue focus:ring-offset-2 focus:ring-offset-brand-bg shadow-md hover:shadow-lg">
                    Sign In
                </button>
            </form>
            
            <div class="mt-8 text-center text-xs text-brand-text">
                <p>Test Accounts:</p>
                <p class="mt-1 font-medium">DRRMO: gquiling / Greg@JHCSC2026</p>
                <p class="font-medium">Clinic: apatigayon / Anzeille@JHCSC2026</p>
            </div>
        </div>
    </div>

</body>
</html>
