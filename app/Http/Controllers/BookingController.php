<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'provider.user', 'service'])
            ->latest()
            ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $providers = Provider::with('user', 'service')->get();

        return view('bookings.create', compact('customers', 'providers'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'provider_id' => 'required|exists:providers,id',
            'booking_date' => 'required|date|after:now',
            'status' => 'nullable|in:pending,confirmed,completed,cancelled',
        ]);

        // Get the service ID from the provider
        $provider = Provider::findOrFail($validated['provider_id']);
        $validated['service_id'] = $provider->service_id;

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'pending';
        }

        $booking = Booking::create($validated);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'provider.user', 'service']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $customers = User::where('role', 'customer')->get();
        $providers = Provider::with('user', 'service')->get();

        return view('bookings.edit', compact('booking', 'customers', 'providers'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'provider_id' => 'required|exists:providers,id',
            'booking_date' => 'required|date',
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        // Get the service ID from the provider
        $provider = Provider::findOrFail($validated['provider_id']);
        $validated['service_id'] = $provider->service_id;

        $booking->update($validated);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated successfully!');
    }

    /**
     * Update the booking status.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Booking status updated successfully!');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully!');
    }
}
