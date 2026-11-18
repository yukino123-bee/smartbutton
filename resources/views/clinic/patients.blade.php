@extends('layouts.clinic')

@section('content')

@if(session('success'))
<div id="flash-msg" class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 rounded-xl text-sm font-bold">{{ session('success') }}</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 4000)</script>
@endif

{{-- Header Row --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="w-8 h-8 rounded-xl bg-teal-100 border border-teal-300 flex items-center justify-center text-teal-700 font-black text-sm">
                🏥
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Clinic Patient Medical Directory</h2>
        </div>
        <p class="text-xs font-bold text-slate-500">History of treated patients and resolved emergency medical cases.</p>
    </div>

    <span class="px-3.5 py-2 bg-teal-50 border border-teal-200 text-teal-800 rounded-xl font-extrabold text-xs flex items-center gap-2">
        <span>{{ $patients->total() ?? 0 }} Total Treated Patients</span>
    </span>
</div>

<div class="space-y-4">
    @forelse($patients as $p)
    @php
        $triageClass = ['Green' => 'bg-emerald-100 text-emerald-800', 'Yellow' => 'bg-amber-100 text-amber-800', 'Red' => 'bg-red-100 text-red-800', 'Black' => 'bg-slate-800 text-white'][$p->triage_level] ?? 'bg-slate-100 text-slate-600';
        $stripe = ['Green' => 'border-l-emerald-500', 'Yellow' => 'border-l-amber-500', 'Red' => 'border-l-red-600', 'Black' => 'border-l-slate-800'][$p->triage_level] ?? 'border-l-slate-300';
    @endphp
    <div class="bg-white border border-slate-300 border-l-4 {{ $stripe }} rounded-2xl shadow-sm overflow-hidden">
        {{-- Top: record, time, status, delete --}}
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 bg-slate-100 border-b border-slate-300">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1">
                <span class="font-mono font-black text-slate-900">#MED-{{ $p->id }}</span>
                <span class="text-xs font-bold text-slate-600">{{ $p->updated_at ? $p->updated_at->format('M d, Y · h:i A') : 'N/A' }}</span>
                <span class="text-xs font-bold text-slate-900">{{ $p->emergency_type }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Treated &amp; Discharged
                </span>
                <form method="POST" action="{{ route('clinic.alerts.destroy', $p->id) }}" onsubmit="return confirmAction(event, 'Delete this patient record? It is only removed from the Clinic; DRRMO keeps its incident history.', 'Delete Record', 'Delete', 'danger')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200 transition-colors cursor-pointer" title="Delete record">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Body: bordered cells so each field is easy to find --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200 text-xs">
            <div class="px-6 py-4">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Patient</div>
                <div class="text-sm font-black text-slate-900 break-words">{{ $p->patient_name ?: 'Not recorded' }}</div>
                <div class="font-mono text-slate-500 break-words">{{ $p->patient_id_number }}</div>
            </div>
            <div class="px-6 py-4">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Triage</div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $triageClass }}">{{ $p->triage_level ?: 'N/A' }}</span>
            </div>
            <div class="px-6 py-4">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Location</div>
                <div class="text-sm font-black text-slate-900">{{ $p->device->building ?? 'Campus Location' }}</div>
                <div class="text-slate-500">{{ $p->device->room ?? '' }} · <span class="font-mono">{{ $p->device->device_code ?? 'N/A' }}</span></div>
            </div>
        </div>
        <div class="border-t border-slate-200 px-6 py-4">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Disposition</div>
            <div class="text-sm font-bold text-slate-800">{{ $p->disposition ?: 'Not recorded' }}</div>
        </div>
        <div class="border-t border-slate-200 px-6 py-4 bg-slate-50/60">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Treatment given</div>
            <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line break-words bg-white border border-slate-200 rounded-xl px-4 py-3">{{ $p->treatment_summary ?: 'Not recorded' }}</p>
        </div>
    </div>
    @empty
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm px-6 py-12 text-center text-slate-500 font-bold">
        No treated patient records logged yet.
    </div>
    @endforelse

    @if($patients->hasPages())
    <div class="px-2 py-2">
        {{ $patients->links() }}
    </div>
    @endif
</div>

@endsection
