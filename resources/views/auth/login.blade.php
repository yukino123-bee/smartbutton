<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Panic Button System</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 font-sans h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800 rounded-xl shadow-2xl border border-slate-700 overflow-hidden">
        <div class="p-8 text-center bg-slate-900/50 border-b border-slate-700">
            <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-slate-800">
                <span class="text-xs font-bold text-black">JHCSC</span>
            </div>
            <h1 class="text-xl font-bold text-white mb-2">System Login</h1>
            <p class="text-xs text-slate-400">Smart Student Panic Button & Emergency Response System</p>
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
                    <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-blue-600 focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-slate-400">Remember me</label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                    Sign In
                </button>
                
                <div class="text-center mt-4">
                    <p class="text-sm text-slate-400">Don't have an account? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 font-medium">Register here</a></p>
                </div>
            </form>
            
            <div class="mt-8 text-center text-xs text-slate-500">
                <p>Test Accounts:</p>
                <p class="mt-1">NDRRMO: ndrrmo / password</p>
                <p>Clinic: clinic / password</p>
            </div>
        </div>
    </div>

</body>
</html>
