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
use Illuminate\Support\Facades\Hash;

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
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'equipment_request' => 'required|array',
                'equipment_request.*' => 'required|in:chair,table,projector,speaker,other',
                'number_of_participants' => 'required|integer|min:1'
            ]);
            
            

            // Check if the requested time slot is already booked
            $existingBooking = DB::connection('tenant')
                ->table('bookings')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                          });
                })
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingBooking) {
                return back()->with('error', 'This time slot is already booked. Please choose another time.');
            }

            // Check if the requested time slot overlaps with any availability
            $existingAvailability = DB::connection('tenant')
                ->table('tenant_availabilities')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                          });
                })
                ->first();

            if ($existingAvailability) {
                return back()->with('error', 'This time slot is marked as unavailable. Please choose another time.');
            }


            if ($tenant->plan == 'basic') {
            // booking can up to 2 weeks in advance
            $maxAdvanceDate = now()->addDays(14);
            if (strtotime($request->start_date) > strtotime($maxAdvanceDate)) {
                return back()->with('error', 'You can only book up to 2 weeks in advance in your plan.');
                
            }


            // Check if booking is on weekend and if allowed
            if (date('N', strtotime($request->start_date)) >= 6) {
                return back()->with('error', 'Weekend bookings are not allowed in your plan.');
            }

            // Check weekly booking limit
            $weeklyBookings = DB::connection('tenant')
                ->table('bookings')
                ->where('user_id', session('user_id'))
                ->where('status', '!=', 'cancelled')
                ->whereBetween('start_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count();

            if ($weeklyBookings >= 2) {
                return back()->with('error', 'You have reached your weekly booking limit of 2 bookings in your plan.');
            }

        }

        if ($tenant->plan == 'premium') {
            // booking can up to 4 weeks in advance
            $maxAdvanceDate = now()->addDays(365);
            if (strtotime($request->start_date) > strtotime($maxAdvanceDate)) {
                return back()->with('error', 'You can only book up to 365 days in advance in your plan.');
            }

            // book unlimited bookings
            $weeklyBookings = DB::connection('tenant')
                ->table('bookings')
                ->where('user_id', session('user_id'))
                ->where('status', '!=', 'cancelled')    
                ->whereBetween('start_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count();

            if ($weeklyBookings >= 10) {
                return back()->with('error', 'You have reached your weekly booking limit of 10 bookings in your plan.');
            }
            
            
        }

        

            \Log::info('Validation passed');

            $equipmentRequests = $request->equipment_request;
            if (in_array('other', $equipmentRequests) && $request->has('other_request')) {
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

            // Get the booking
            $booking = DB::connection('tenant')
                ->table('bookings')
                ->where('id', $id)
                ->where('user_id', session('user_id'))
                ->first();

            if (!$booking) {
                return back()->with('error', 'Booking not found.');
            }

            return view('user.my-booking.edit', compact('booking'));

        } catch (\Exception $e) {
            \Log::error('Error in editBooking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'domain' => $domain ?? null
            ]);
            return back()->with('error', 'Failed to load booking data. Please try again.');
        }
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
                'start_date' => 'required|date|after:now',
                'end_date' => 'required|date|after:start_date',
                'equipment_request' => 'required|array',
                'equipment_request.*' => 'required|in:chair,table,projector,speaker,other',
                'number_of_participants' => 'required|integer|min:1'
            ]);

            // Check if the requested time slot is already booked (excluding current booking)
            $existingBooking = DB::connection('tenant')
                ->table('bookings')
                ->where('id', '!=', $id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                          });
                })
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingBooking) {
                return back()->with('error', 'This time slot is already booked. Please choose another time.');
            }

            // Check if the requested time slot overlaps with any availability
            $existingAvailability = DB::connection('tenant')
                ->table('tenant_availabilities')
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function($q) use ($request) {
                              $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                          });
                })
                ->first();

            if ($existingAvailability) {
                return back()->with('error', 'This time slot is marked as unavailable. Please choose another time.');
            }

            // Check if booking is within allowed advance booking days
            // $maxAdvanceDate = now()->addDays($tenant->advance_booking_days);
            // if (strtotime($request->start_date) > strtotime($maxAdvanceDate)) {
            //     return back()->with('error', 'You can only book up to ' . $tenant->advance_booking_days . ' days in advance.');
            // }

            // Check if booking is on weekend and if allowed
            if (date('N', strtotime($request->start_date)) >= 6) {
                return back()->with('error', 'Weekend bookings are not allowed.');
            }

            // Check weekly booking limit (excluding current booking)
            $weeklyBookings = DB::connection('tenant')
                ->table('bookings')
                ->where('user_id', session('user_id'))
                ->where('id', '!=', $id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('start_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count();

            if ($weeklyBookings >= 2) {
                return back()->with('error', 'You have reached your weekly booking limit of 2 bookings.');
            }

            $equipmentRequests = $request->equipment_request;
            if (in_array('other', $equipmentRequests) && $request->has('other_request')) {
                $equipmentRequests = array_map(function($item) use ($request) {
                    return $item === 'other' ? $request->other_request : $item;
                }, $equipmentRequests);
            }

            $booking = Booking::on('tenant')->findOrFail($id);
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
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to update booking. Please try again.');
        }
    }
    
    public function deleteBooking($id)
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

            // Delete the booking
            $deleted = DB::connection('tenant')
                ->table('bookings')
                ->where('id', $id)
                ->where('user_id', session('user_id'))
                ->delete();

            if (!$deleted) {
                return back()->with('error', 'Booking not found or you do not have permission to delete it.');
            }

            return redirect()->route('user.my-booking.index')
                ->with('success', 'Booking deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Error deleting booking', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $id,
                'user_id' => session('user_id')
            ]);
            return back()->with('error', 'Failed to delete booking. Please try again.');
        }
    }

    public function deleteBookingHistory($id)
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

            // Delete the booking
            $deleted = DB::connection('tenant')
                ->table('bookings')
                ->where('id', $id)
                ->where('user_id', session('user_id'))
                ->delete();

            if (!$deleted) {
                return back()->with('error', 'Booking not found or you do not have permission to delete it.');
            }

            return redirect()->route('user.booking-history.index')
                ->with('success', 'Booking deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Error deleting booking history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $id,
                'user_id' => session('user_id')
            ]);
            return back()->with('error', 'Failed to delete booking. Please try again.');
        }
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

    public function showBookingHistory($id)
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
        
        $booking = DB::connection('tenant')
            ->table('bookings')
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }
        
        return view('user.booking-history.show', compact('booking'));
    }

    public function profile()
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

        $user = TenantUser::find(session('user_id'));
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant_users,email,' . session('user_id'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = TenantUser::find(session('user_id'));
        $user->update([
            'first_name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->phone,
            'address' => $request->address,
        ]);

        // Update session data
        session(['user_name' => $request->name]);
        session(['user_email' => $request->email]);

        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }

    public function settings()
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

        $user = TenantUser::find(session('user_id'));
        return view('user.settings', compact('user'));
    }

    public function updateSettings(Request $request)
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

        $user = TenantUser::find(session('user_id'));

        if ($request->has('current_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return redirect()->route('user.settings')->with('success', 'Password updated successfully.');
        }

        if ($request->has('email_notifications') || $request->has('sms_notifications')) {
            $user->update([
                'email_notifications' => $request->has('email_notifications'),
                'sms_notifications' => $request->has('sms_notifications')
            ]);

            return redirect()->route('user.settings')->with('success', 'Notification preferences updated successfully.');
        }

        return back()->with('error', 'No changes were made.');
    }
}







