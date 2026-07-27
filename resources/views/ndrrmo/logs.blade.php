@extends('layouts.ndrrmo')

@section('content')
<form id="logs-bulk-form" action="{{ route('ndrrmo.alerts.bulk-delete') }}" method="POST">
    @csrf
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 mb-1">Incident Logs</h2>
            <p class="text-xs text-slate-500">Complete history of all reported emergencies and responses.</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-lg px-3 py-2 shadow-sm hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" id="header-select-all" onclick="toggleAllLogs(this)" class="w-4 h-4 rounded text-red-600 focus:ring-red-500 cursor-pointer">
                <span>Select All</span>
            </label>

            <button type="submit" id="header-delete-btn" disabled
                    class="bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg flex items-center px-4 py-2 text-xs font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span>Delete Selected (<span id="header-selected-count">0</span>)</span>
            </button>

            <a href="{{ route('ndrrmo.reports.export-excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg flex items-center px-4 py-2 text-xs font-bold transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export to Excel
            </a>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl flex flex-col overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-500 uppercase tracking-wider font-bold">
                        <th class="p-4 w-10 text-center">
                            <span class="sr-only">Select</span>
                        </th>
                        <th class="px-4 py-3 font-bold">Time Reported</th>
                        <th class="px-4 py-3 font-bold">Incident Type</th>
                        <th class="px-4 py-3 font-bold">Location</th>
                        <th class="px-4 py-3 font-bold">Device ID</th>
                        <th class="px-4 py-3 font-bold">Status</th>
                        <th class="px-4 py-3 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-800 divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-4 text-center">
                            <input type="checkbox" name="ids[]" value="{{ $log->id }}" onchange="updateLogSelectionCount()" class="log-checkbox w-4 h-4 rounded text-red-600 focus:ring-red-500 cursor-pointer">
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-[10px] text-slate-500 font-mono">{{ $log->created_at->format('h:i:s A') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $bg = 'bg-slate-600';
                                if (str_contains(strtolower($log->emergency_type), 'medical')) $bg = 'bg-orange-500';
                                elseif (str_contains(strtolower($log->emergency_type), 'critical') || str_contains(strtolower($log->emergency_type), 'general')) $bg = 'bg-red-600';
                                elseif (str_contains(strtolower($log->emergency_type), 'public')) $bg = 'bg-amber-500';
                            @endphp
                            <span class="{{ $bg }} text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider shadow-xs">
                                {{ $log->emergency_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $log->device->building ?? 'Campus Location' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $log->device->floor ?? '' }} {{ $log->device->room ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-slate-600">{{ $log->device->device_code ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusColor = 'text-slate-600';
                                if (strtolower($log->status) === 'pending') $statusColor = 'text-red-600';
                                elseif (strtolower($log->status) === 'responding') $statusColor = 'text-amber-600';
                                elseif (strtolower($log->status) === 'resolved') $statusColor = 'text-emerald-600';
                            @endphp
                            <span class="{{ $statusColor }} font-black text-[11px] uppercase tracking-wider flex items-center">
                                @if(in_array(strtolower($log->status), ['pending', 'responding']))
                                <span class="w-2 h-2 rounded-full bg-current mr-1.5 animate-ping"></span>
                                @endif
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="deleteSingleLog({{ $log->id }})" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors" title="Delete Log">
                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No incident logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $logs->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</form>

<script>
const headerSelectAll = document.getElementById('header-select-all');
const headerDeleteBtn = document.getElementById('header-delete-btn');
const headerSelCount  = document.getElementById('header-selected-count');

function getCheckedLogs() {
    return [...document.querySelectorAll('.log-checkbox:checked')];
}

function updateLogSelectionCount() {
    const checked = getCheckedLogs();
    const count   = checked.length;
    const all     = document.querySelectorAll('.log-checkbox');

    if (headerSelCount) headerSelCount.textContent = count;
    if (headerDeleteBtn) headerDeleteBtn.disabled  = (count === 0);

    if (headerSelectAll && all.length > 0) {
        headerSelectAll.checked       = (count === all.length);
        headerSelectAll.indeterminate = (count > 0 && count < all.length);
    }
}

function toggleAllLogs(master) {
    document.querySelectorAll('.log-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
    updateLogSelectionCount();
}

function deleteSingleLog(id) {
    if (confirm('Are you sure you want to delete this incident log?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("ndrrmo.alerts.bulk-delete") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="ids[]" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

document.getElementById('logs-bulk-form').addEventListener('submit', function(e) {
    const count = getCheckedLogs().length;
    if (count === 0) { e.preventDefault(); return; }
    if (!confirm(`Delete ${count} incident log(s)? This cannot be undone.`)) {
        e.preventDefault();
    }
});

// Keyboard shortcuts (Ctrl+A = Select All, Esc = Deselect)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
        const targetTag = e.target.tagName.toLowerCase();
        if (targetTag === 'input' || targetTag === 'textarea') return;
        e.preventDefault();
        const master = headerSelectAll;
        if (!master) return;
        const newState = !master.checked;
        master.checked = newState;
        toggleAllLogs(master);
    }
    if (e.key === 'Escape') {
        if (headerSelectAll) headerSelectAll.checked = false;
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = false);
        updateLogSelectionCount();
    }
});
</script>
@endsection
