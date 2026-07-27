<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Panic Button System</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-200 font-sans min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800 rounded-xl shadow-2xl border border-slate-700 overflow-hidden my-8">
        <div class="p-8 text-center bg-slate-900/50 border-b border-slate-700">
            <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-slate-800">
                <span class="text-xs font-bold text-black">JHCSC</span>
            </div>
            <h1 class="text-xl font-bold text-white mb-2">Create an Account</h1>
            <p class="text-xs text-slate-400">Join the Smart Student Panic Button System</p>
        </div>
        
        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-3 rounded-lg mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label for="fullname" class="block text-sm font-medium text-slate-300 mb-1">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" required autofocus
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-300 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-300 mb-1">Role</label>
                    <select id="role" name="role" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role...</option>
                        <option value="NDRRMO" {{ old('role') == 'NDRRMO' ? 'selected' : '' }}>NDRRMO</option>
                        <option value="Clinic" {{ old('role') == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 mt-2">
                    Create Account
                </button>
                
                <div class="text-center mt-4">
                    <p class="text-sm text-slate-400">Already have an account? <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-400 font-medium">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
