<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantAvailabilityController extends Controller
{
    public function index()
    {


        $availabilities = TenantAvailability::where('tenant_id', session('tenant')->id)
            ->orderBy('start_date', 'asc')
            ->get();

        return view('tenant.availability.index', compact('availabilities'));
    }

    public function create()
    {
        return view('tenant.availability.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        TenantAvailability::create([
            'tenant_id' => session('tenant')->id,
            'event_name' => $request->event_name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('tenant.availability.index')
            ->with('success', 'Availability request submitted successfully.');
    }

    public function show(TenantAvailability $availability)
    {
        if ($availability->tenant_id !== session('tenant')->id) {
            abort(403);
        }

        return view('tenant.availability.show', compact('availability'));
    }

    public function destroy($id)
    {
        $availability = TenantAvailability::where('tenant_id', session('tenant_id'))
            ->findOrFail($id);

        $availability->delete();

        return redirect()->route('tenant.availability.index')
            ->with('success', 'Availability request deleted successfully.');
            
    }

    public function editAvailability($id)
    {
        $availability = TenantAvailability::where('tenant_id', session('tenant_id'))
            ->findOrFail($id);
        return view('tenant.availability.edit', compact('availability'));
    }

    public function updateAvailability(Request $request, $id)
    {
        try {
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            $tenant = \App\Models\Tenant::where('domain', $domain)
                           ->where('status', 'accepted')
                           ->first();

            if (!$tenant) {
                return back()->with('error', 'Invalid tenant domain.');
            }

            // Set the tenant's database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);

            $availability = TenantAvailability::where('tenant_id', $tenant->id)
                ->findOrFail($id);

            $data = [
                'event_name' => $request->event_name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ];

            $availability->update($data);

            return redirect()->route('tenant.availability.index')
                ->with('success', 'Availability updated successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating availability', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to update availability. Please try again.']);
        }
    }
} 