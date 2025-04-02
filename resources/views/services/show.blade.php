@foreach ($providers as $provider)
    <div class="font-bold text-blue-500 text-sm">the provider is {{ $provider->user->first_name }}</div>
    <div>
        <strong> the service is : {{ $provider->service->name }}</strong> price : {{ $provider->service->price }}
    </div>

    <hr>
@endforeach
