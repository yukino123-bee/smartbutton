@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-brand-dark text-2xl font-bold">Device Management</h1>
        <p class="text-brand-text text-sm mt-1">Register, monitor, and manage ESP32 panic buttons across the campus.</p>
    </div>
    <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')" class="bg-brand-red hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center shadow-[0_0_15px_rgba(239,68,68,0.3)]">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Register Device
    </button>
</div>

@if(session('success'))
<div class="mb-6 bg-brand-green/20 border border-brand-green/50 text-brand-green px-4 py-3 rounded-lg flex items-center">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-brand-red/20 border border-brand-red/50 text-brand-red px-4 py-3 rounded-lg">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-brand-card border border-brand-border rounded-xl overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-black/40 border-b border-brand-border text-xs uppercase tracking-wider text-brand-text">
                    <th class="p-4 font-medium">Device Code</th>
                    <th class="p-4 font-medium">Location</th>
                    <th class="p-4 font-medium">Coordinates</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium">Last Seen</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($devices as $device)
                <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors">
                    <td class="p-4">
                        <div class="font-bold text-brand-dark flex items-center">
                            <svg class="w-4 h-4 mr-2 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $device->device_code }}
                        </div>
                    </td>
                    <td class="p-4 text-brand-text">
                        <div class="text-brand-dark">{{ $device->building }}</div>
                        <div class="text-xs">{{ $device->floor }}, {{ $device->room }}</div>
                    </td>
                    <td class="p-4 text-brand-text text-xs font-mono">
                        {{ $device->latitude ?? 'N/A' }}, <br>{{ $device->longitude ?? 'N/A' }}
                    </td>
                    <td class="p-4">
                        @if($device->status === 'active')
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold tracking-wider uppercase bg-brand-green/20 text-brand-green border border-brand-green/30"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>Active</span>
                        @elseif($device->status === 'maintenance')
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold tracking-wider uppercase bg-yellow-500/20 text-yellow-500 border border-yellow-500/30"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>Maintenance</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold tracking-wider uppercase bg-brand-border text-brand-text border border-brand-border/50"><span class="w-1.5 h-1.5 rounded-full bg-brand-text mr-1.5"></span>Inactive</span>
                        @endif
                    </td>
                    <td class="p-4 text-brand-text text-xs">
                        @if($device->last_seen)
                            <div class="text-brand-dark">{{ \Carbon\Carbon::parse($device->last_seen)->diffForHumans() }}</div>
                            <div class="text-[10px]">{{ \Carbon\Carbon::parse($device->last_seen)->format('M d, Y h:i A') }}</div>
                        @else
                            <span class="text-brand-text italic">Never connected</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <button onclick="editDevice({{ $device }})" class="text-brand-blue hover:text-blue-400 mr-3 transition-colors text-sm font-medium">Edit</button>
                        <form action="{{ route('ndrrmo.devices.destroy', $device->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this device? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-brand-red hover:text-red-400 transition-colors text-sm font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-brand-hover flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </div>
                            <h3 class="text-lg font-medium text-brand-dark mb-1">No Devices Registered</h3>
                            <p class="text-brand-text text-sm mb-4">You haven't added any ESP32 panic buttons to the system yet.</p>
                            <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')" class="bg-brand-red hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">Register First Device</button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Device Modal -->
<div id="addDeviceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark/50 backdrop-blur-sm transition-opacity">
    <div class="bg-brand-card border border-brand-border rounded-xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all">
        <div class="p-4 border-b border-brand-border flex justify-between items-center bg-brand-bg">
            <h3 class="text-lg font-bold text-brand-dark flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Register New Device
            </h3>
            <button onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="text-brand-text hover:text-brand-dark transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('ndrrmo.devices.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Device Code</label>
                    <input type="text" name="device_code" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="e.g. ENG-001">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Building</label>
                    <input type="text" name="building" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="e.g. Engineering Building">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Floor</label>
                        <input type="text" name="floor" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="e.g. 2nd Floor">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Room</label>
                        <input type="text" name="room" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="e.g. Room 203">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Latitude</label>
                        <input type="text" name="latitude" class="w-full bg-black/40 border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark font-mono text-sm focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="10.123456">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Longitude</label>
                        <input type="text" name="longitude" class="w-full bg-black/40 border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark font-mono text-sm focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all" placeholder="124.123456">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Initial Status</label>
                    <select name="status" class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-red focus:ring-1 focus:ring-brand-red transition-all appearance-none">
                        <option value="active">Active (Ready)</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="px-4 py-2 text-brand-text hover:text-brand-dark mr-3 font-medium transition-colors">Cancel</button>
                <button type="submit" class="bg-brand-red hover:bg-red-600 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-[0_0_15px_rgba(239,68,68,0.3)]">Save Device</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Device Modal -->
<div id="editDeviceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-dark/50 backdrop-blur-sm transition-opacity">
    <div class="bg-brand-card border border-brand-border rounded-xl w-full max-w-md overflow-hidden shadow-2xl transform transition-all">
        <div class="p-4 border-b border-brand-border flex justify-between items-center bg-brand-bg">
            <h3 class="text-lg font-bold text-brand-dark flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Device
            </h3>
            <button onclick="document.getElementById('editDeviceModal').classList.add('hidden')" class="text-brand-text hover:text-brand-dark transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Device Code</label>
                    <input type="text" name="device_code" id="edit_device_code" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Building</label>
                    <input type="text" name="building" id="edit_building" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Floor</label>
                        <input type="text" name="floor" id="edit_floor" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Room</label>
                        <input type="text" name="room" id="edit_room" required class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Latitude</label>
                        <input type="text" name="latitude" id="edit_latitude" class="w-full bg-black/40 border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark font-mono text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Longitude</label>
                        <input type="text" name="longitude" id="edit_longitude" class="w-full bg-black/40 border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark font-mono text-sm focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-brand-text mb-1 uppercase tracking-wider">Status</label>
                    <select name="status" id="edit_status" class="w-full bg-brand-bg border border-brand-border rounded-lg px-4 py-2.5 text-brand-dark focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue transition-all appearance-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="document.getElementById('editDeviceModal').classList.add('hidden')" class="px-4 py-2 text-brand-text hover:text-brand-dark mr-3 font-medium transition-colors">Cancel</button>
                <button type="submit" class="bg-brand-blue hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-[0_0_15px_rgba(59,130,246,0.3)]">Update Details</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editDevice(device) {
        document.getElementById('edit_device_code').value = device.device_code;
        document.getElementById('edit_building').value = device.building;
        document.getElementById('edit_floor').value = device.floor;
        document.getElementById('edit_room').value = device.room;
        document.getElementById('edit_latitude').value = device.latitude || '';
        document.getElementById('edit_longitude').value = device.longitude || '';
        document.getElementById('edit_status').value = device.status;
        
        document.getElementById('editForm').action = `/ndrrmo/devices/${device.id}`;
        document.getElementById('editDeviceModal').classList.remove('hidden');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('addDeviceModal')) {
            document.getElementById('addDeviceModal').classList.add('hidden');
        }
        if (event.target == document.getElementById('editDeviceModal')) {
            document.getElementById('editDeviceModal').classList.add('hidden');
        }
    }
</script>
@endsection
