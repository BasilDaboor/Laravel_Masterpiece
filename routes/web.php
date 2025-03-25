<?php

use Illuminate\Support\Facades\Route;
// use App\Models\User;
// use App\Models\Service;
use App\Models\Provider;

Route::get('/', function () {
    // $providers = User::where('role', 'provider')->get();
    $providers = Provider::with('service','user')->get();
    // $providers = Provider::all();
    // dd($servecies);
    return view('welcome', ['providers' => $providers]);
});

Route::get('/service/{id}', function ($id) {
    // $service = Service::find($id);
    $providers = Provider::with('service','user')->where('service_id', $id)->get();
    // dd($service);
    return view('services.show', ['providers' => $providers]);
});
