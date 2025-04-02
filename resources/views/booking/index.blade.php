@foreach ($booking as $book)
    <div class="font-bold text-blue-500 text-sm"> {{ $book->user->first_name }}</div> <br>
    <div>
        <strong>{{ $book->service->name }}</strong> pays {{ $book->service->price }} per year
    </div>
    <div>
        <strong>The status is :{{ $book->status }}</strong> provider name :{{ $book->provider->user->first_name }}
    </div>

    <hr>
@endforeach
