@extends('layouts.clinic')

@section('content')

{{-- Success Flash Message --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-800 rounded-2xl text-xs font-extrabold animate-fade-in shadow-sm">
    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span>{{ session('success') }}</span>
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000)</script>
@endif

{{-- Header Row --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="w-8 h-8 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center text-blue-600 font-black text-sm">
                ↓
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Incoming Patients & Emergencies</h2>
        </div>
        <p class="text-xs font-bold text-slate-500">Monitor patients being dispatched or transported to the campus medical clinic.</p>
    </div>

    <span class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl font-extrabold text-xs flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
        <span>{{ $incomingPatients->count() }} Patient(s) En Route</span>
    </span>
</div>

@if($incomingPatients->isEmpty())
<div class="bg-white border border-slate-200 rounded-3xl p-12 text-center flex flex-col items-center justify-center shadow-sm">
    <div class="w-16 h-16 bg-blue-50 border border-blue-200 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
    </div>
    <h3 class="text-slate-900 font-black text-lg mb-1">No Incoming Patients</h3>
    <p class="text-slate-500 text-xs font-bold">No emergency patients are currently en route to the clinic.</p>
</div>
@else
<div class="space-y-4">
    @foreach($incomingPatients as $patient)
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-start gap-4 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-red-100 border border-red-200 flex items-center justify-center text-red-600 font-black text-xl shrink-0 shadow-sm">
                    🚑
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <span class="text-base font-black text-slate-900">{{ $patient->device->building ?? 'Campus Location' }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700 border border-red-200">
                            {{ $patient->emergency_type }}
                        </span>
                    </div>
                    <p class="text-xs font-bold text-slate-500">
                        Device: <span class="font-mono text-slate-800 font-black">{{ $patient->device->device_code ?? 'N/A' }}</span>
                        • Room: {{ $patient->device->room ?? 'N/A' }} ({{ $patient->device->floor ?? 'N/A' }})
                    </p>
                    <p class="text-[11px] font-medium text-slate-400">
                        Reported at: {{ $patient->created_at ? $patient->created_at->format('h:i:s A · M d, Y') : '' }}
                    </p>
                </div>
            </div>

            <!-- Preparation Checklist & Action -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full lg:w-auto">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 text-[11px] font-bold text-slate-700 space-y-1">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">CLINIC STANDBY CHECKLIST</div>
                    <div class="flex items-center gap-1.5 text-emerald-700">
                        <span>✓ First Aid Kit Prepared</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-emerald-700">
                        <span>✓ Stretcher / Bed Reserved</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('clinic.incidents.resolve', $patient->id) }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto py-3 px-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs transition-all shadow-md shadow-emerald-200 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Patient Arrived & Treated</span>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif

@endsection
