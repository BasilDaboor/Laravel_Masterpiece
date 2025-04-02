<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    /**
     * Display a listing of providers.
     */
    public function index()
    {
        $providers = Provider::with(['user', 'service'])->paginate(15);
        return view('providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new provider.
     */
    public function create()
    {
        $users = User::where('role', 'provider')
            ->whereDoesntHave('provider')
            ->get();

        $services = Service::all();

        return view('providers.create', compact('users', 'services'));
    }

    /**
     * Store a newly created provider in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('providers', 'user_id'),
            ],
            'service_id' => 'required|exists:services,id',
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Set initial rating to 0
        $validated['rating'] = 0;

        $provider = Provider::create($validated);

        // Update user role if needed
        $user = User::find($validated['user_id']);
        if ($user->role !== 'provider') {
            $user->update(['role' => 'provider']);
        }

        return redirect()->route('providers.show', $provider)->with('success', 'Provider created successfully!');
    }

    /**
     * Display the specified provider.
     */
    public function show(Provider $provider)
    {
        $provider->load(['user', 'service', 'bookings']);
        $reviews = $provider->reviews()->with('user')->latest()->paginate(5);

        return view('providers.show', compact('provider', 'reviews'));
    }

    /**
     * Show the form for editing the specified provider.
     */
    public function edit(Provider $provider)
    {
        $services = Service::all();
        return view('providers.edit', compact('provider', 'services'));
    }

    /**
     * Update the specified provider in storage.
     */
    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $provider->update($validated);

        return redirect()->route('providers.show', $provider)->with('success', 'Provider updated successfully!');
    }

    /**
     * Remove the specified provider from storage.
     */
    public function destroy(Provider $provider)
    {
        // Check if provider has bookings
        if ($provider->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete provider as they have bookings.');
        }

        $provider->delete();

        return redirect()->route('providers.index')->with('success', 'Provider deleted successfully!');
    }
}
