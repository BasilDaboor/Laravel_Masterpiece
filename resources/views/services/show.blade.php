@foreach ($providers as $provider)
    <div class="font-bold text-blue-500 text-sm"> {{$provider->user->first_name}}</div>
    <div>
        <strong>{{$provider->service->name}}</strong> pays {{$provider->service->price}} per year
    </div>
    
    <hr>
@endforeach