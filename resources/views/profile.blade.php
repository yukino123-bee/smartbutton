@extends(auth()->user()->role === 'Clinic' ? 'layouts.clinic' : 'layouts.ndrrmo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-8">
    <!-- Header Banner Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 p-1 shadow-md shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?? $user->username) }}&background=2563EB&color=fff&size=128" alt="{{ $user->fullname }}" class="w-full h-full object-cover rounded-xl">
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-black text-slate-900 leading-tight">{{ $user->fullname }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                        {{ $user->role }} Admin
                    </span>
                </div>
                <p class="text-xs font-bold text-slate-500 flex items-center gap-2">
                    <span>@<span>{{ $user->username }}</span></span>
                    <span>•</span>
                    <span class="text-emerald-600 font-extrabold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                        Active Account
                    </span>
                </p>
                <p class="text-[11px] font-medium text-slate-400 mt-1">
                    Administrator since {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ auth()->user()->role === 'Clinic' ? route('clinic.dashboard') : route('ndrrmo.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Notifications / Success Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-bold px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-800 text-xs font-bold px-4 py-3 rounded-2xl space-y-1 shadow-sm">
            <div class="flex items-center gap-2 font-black">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Please fix the errors below:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 text-[11px] font-semibold text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Form 1: General Account Information -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Account Profile</h3>
                        <p class="text-[11px] font-bold text-slate-500">Update your administrator profile details</p>
                    </div>
                </div>

                <form method="POST" action="{{ auth()->user()->role === 'Clinic' ? route('clinic.profile.update') : route('ndrrmo.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="fullname" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                        <input type="text" name="fullname" id="fullname" value="{{ old('fullname', $user->fullname) }}" required
                               class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label for="username" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">Username</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" required
                               class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-1">Role</label>
                            <input type="text" value="{{ $user->role }}" disabled
                                   class="w-full px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-1">Status</label>
                            <input type="text" value="{{ ucfirst($user->status ?? 'active') }}" disabled
                                   class="w-full px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs shadow-md shadow-blue-200 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form 2: Password & Security -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Security & Password</h3>
                        <p class="text-[11px] font-bold text-slate-500">Change your login password</p>
                    </div>
                </div>

                <form method="POST" action="{{ auth()->user()->role === 'Clinic' ? route('clinic.profile.password') : route('ndrrmo.profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">New Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="Minimum 8 characters">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all" placeholder="Re-type new password">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] text-white font-extrabold text-xs shadow-md shadow-slate-300 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
