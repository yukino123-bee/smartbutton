@extends('layouts.clinic')

@section('content')

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

<div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] text-slate-600 font-black uppercase tracking-wider">
                    <th class="px-6 py-4">Record ID</th>
                    <th class="px-6 py-4">Date Treated</th>
                    <th class="px-6 py-4">Emergency Type</th>
                    <th class="px-6 py-4">Location</th>
                    <th class="px-6 py-4">Device Origin</th>
                    <th class="px-6 py-4">Medical Status</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100 font-medium text-slate-800">
                @forelse($patients as $p)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-6 py-4 font-mono font-black text-slate-900">#MED-{{ $p->id }}</td>
                    <td class="px-6 py-4 font-bold text-slate-700">
                        {{ $p->updated_at ? $p->updated_at->format('M d, Y · h:i A') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">
                        {{ $p->emergency_type }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">
                        {{ $p->device->building ?? 'Campus Location' }}
                        <span class="text-[11px] text-slate-400 block font-normal">{{ $p->device->room ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-slate-800">
                        {{ $p->device->device_code ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Treated & Discharged
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-bold">
                        No treated patient records logged yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        {{ $patients->links() }}
    </div>
    @endif
</div>

@endsection
