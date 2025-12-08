<div class="d-flex">
    @if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin')
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-primary mx-1">Edit</a>
    @endif



    @if (auth()->guard('web')->user() && auth()->guard('web')->user()->role == 'admin')
        <button type="submit" class="btn btn-sm btn-danger client-delete" client_id="{{ $client->id }}">
            Delete
        </button>
    @endif

</div>


