<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantUser;
use App\Models\Booking;
use App\Models\TenantAvailability;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Availability;
use Illuminate\Support\Facades\Log;

class TenantUserController extends Controller
{
    public function dashboard()
    {

       
         // Get the tenant from the domain
         $domain = request()->getHost();
         $domain = str_replace('.localhost', '', $domain);
         
         $tenant = Tenant::where('domain', $domain)
                        ->where('status', 'accepted')
                        ->first();
 
         if (!$tenant) {
             return back()->with('error', 'Invalid tenant domain.');
         }
 
         // Set the tenant's database connection
         Config::set('database.connections.tenant.database', $tenant->database_name);
         DB::purge('tenant');
         DB::reconnect('tenant');
 
         // Get availabilities for this tenant
         $bookings = DB::connection('tenant')
             ->table('bookings')
             ->where('user_id', session('user_id'))
             ->orderBy('created_at', 'desc')
             ->where('status', 'pending')
             ->get()->take(1);

             $allBookings = DB::connection('tenant')
             ->table('bookings')
             ->where('user_id', session('user_id'))
             ->orderBy('created_at', 'desc')
             ->get()->count();
 
         return view('user.dashboard', compact('bookings', 'allBookings'));
    }

    public function myBooks()
    {
         // Get the tenant from the domain
         $domain = request()->getHost();
         $domain = str_replace('.localhost', '', $domain);
         
         $tenant = Tenant::where('domain', $domain)
                        ->where('status', 'accepted')
                        ->first();
 
         if (!$tenant) {
             return back()->with('error', 'Invalid tenant domain.');
         }
 
         // Set the tenant's database connection
         Config::set('database.connections.tenant.database', $tenant->database_name);
         DB::purge('tenant');
         DB::reconnect('tenant');

         // chair convert to Chair
         
 
         // Get availabilities for this tenant
         $bookings = DB::connection('tenant')
             ->table('bookings')
             ->where('user_id', session('user_id'))
             ->where('status', 'pending')
             ->orderBy('created_at', 'desc')
             ->paginate(10);
 
         return view('user.my-booking.index', compact('bookings'));
    }

    public function createBooking()
    {
        return view('user.my-booking.create');
    }

    public function storeBooking(Request $request)
    {
        try {
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            $tenant = Tenant::where('domain', $domain)
                           ->where('status', 'accepted')
                           ->first();

            if (!$tenant) {
                return back()->with('error', 'Invalid tenant domain.');
            }

            // Set the tenant's database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            \Log::info('Starting booking creation process', [
                'request_data' => $request->all(),
                'user_id' => session('user_id'),
                'user_name' => session('user_name'),
                'user_email' => session('user_email'),
                'tenant_database' => $tenant->database_name
            ]);

            $request->validate([
                'event_name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'equipment_request' => 'required|array',
                'equipment_request.*' => 'required|in:chair,table,projector,speaker,other',
                'number_of_participants' => 'required|integer|min:1'
            ]);

            \Log::info('Validation passed');

            $equipmentRequests = $request->equipment_request;
            if (in_array('other', $equipmentRequests) && $request->has('other_request')) {
                // Replace 'other' with the custom input
                $equipmentRequests = array_map(function($item) use ($request) {
                    return $item === 'other' ? $request->other_request : $item;
                }, $equipmentRequests);
            }

            $booking = new Booking();
            $booking->setConnection('tenant');
            $booking->fill([
                'user_id' => session('user_id'),
                'name' => session('user_name'),
                'event_name' => $request->event_name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'equipment_request' => implode(', ', $equipmentRequests),
                'number_of_participants' => $request->number_of_participants,
                'status' => 'pending'
            ]);
            $booking->save();

            \Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'event_name' => $booking->event_name,
                'tenant_database' => $tenant->database_name
            ]);

            return redirect()->route('user.my-booking.index')
                ->with('success', 'Booking request submitted successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in booking creation', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error creating booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to create booking. Please try again.']);
        }
    }

    public function editBooking($id)
    {
        $booking = Booking::findOrFail($id);
        return view('user.my-booking.edit', compact('booking'));
    }

    public function updateBooking(Request $request, $id)
    {
        try {
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            $tenant = Tenant::where('domain', $domain)
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
                'event_name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'equipment_request' => 'required|array',
                'equipment_request.*' => 'required|in:chair,table,projector,speaker,other',
                'number_of_participants' => 'required|integer|min:1'
            ]);

            $equipmentRequests = $request->equipment_request;
            if (in_array('other', $equipmentRequests) && $request->has('other_request')) {
                // Replace 'other' with the custom input
                $equipmentRequests = array_map(function($item) use ($request) {
                    return $item === 'other' ? $request->other_request : $item;
                }, $equipmentRequests);
            }

            $booking = Booking::findOrFail($id);
            $booking->update([
                'event_name' => $request->event_name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'equipment_request' => implode(', ', $equipmentRequests),
                'number_of_participants' => $request->number_of_participants
            ]);

            return redirect()->route('user.my-booking.index')
                ->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to update booking. Please try again.']);
        }
    }
    
    public function deleteBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('user.my-booking.index')->with('success', 'Booking deleted successfully.');
    }

    public function deleteBookingHistory($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('user.booking-history.index')->with('success', 'Booking deleted successfully.');
    }

    public function checkAvailability()
    {
        try {
            // Get the tenant from the domain
            $domain = request()->getHost();
            $domain = str_replace('.localhost', '', $domain);
            
            \Log::info('Checking availability for domain', ['domain' => $domain]);
            
            $tenant = Tenant::where('domain', $domain)
                           ->where('status', 'accepted')
                           ->first();

            if (!$tenant) {
                \Log::error('Invalid tenant domain', ['domain' => $domain]);
                return back()->with('error', 'Invalid tenant domain.');
            }

            // Set the tenant's database connection
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');

            \Log::info('Database connection set', [
                'tenant_database' => $tenant->database_name,
                'domain' => $domain
            ]);

            // Get availabilities for this tenant
            $availabilities = DB::connection('tenant')
                ->table('tenant_availabilities')
                ->where('tenant_id', $tenant->id)
                ->orderBy('start_date', 'asc')
                ->get();

            $bookings = DB::connection('tenant')
                ->table('bookings')
                ->orderBy('created_at', 'desc')
                ->where('status', 'confirmed')
                ->get();

            \Log::info('Data retrieved', [
                'availabilities_count' => $availabilities->count(),
                'bookings_count' => $bookings->count(),
                'tenant_database' => $tenant->database_name
            ]);

            return view('user.check-availability', compact('availabilities', 'bookings'));

        } catch (\Exception $e) {
            \Log::error('Error in checkAvailability', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'domain' => $domain ?? null
            ]);
            return back()->with('error', 'Failed to load availability data. Please try again.');
        }
    }

    public function bookingHistory()
    {

        // Get the tenant from the domain
        $domain = request()->getHost();
        $domain = str_replace('.localhost', '', $domain);
        
        $tenant = Tenant::where('domain', $domain)
                       ->where('status', 'accepted')
                       ->first();

        if (!$tenant) {
            return back()->with('error', 'Invalid tenant domain.');
        }

        // Set the tenant's database connection
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Get availabilities for this tenant
        $bookings = DB::connection('tenant')
            ->table('bookings')
            ->where('user_id', session('user_id'))
            ->whereIn('status', ['confirmed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        

        return view('user.booking-history.index', compact('bookings'));
        
    }
    

}







