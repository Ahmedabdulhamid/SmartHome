<div>
    <div>
        <ul>
            @foreach ($history as $item)
                <li class='p-3'>
                    <div class="text-primart fw-bold">
                      {{ auth()->guard('web')->user()?->name }}
                    </div>
                    <p class="text-secondry">{{ $item['q'] }} </p>
                </li>
                <li class='p-3'>
                    <div class="text-primart fw-bold">
                      Google Ai
                    </div>
                    <p class="text-secondry">{{ $item['a'] }} </p>
                </li>
            @endforeach
        </ul>
    </div>
    <form wire:submit.prevent="submit" class="php-email-form">
        <div class="row gy-4">

            <div class="col-12">
                <textarea type="text" class="form-control"wire:model='prompt' rows="8"></textarea>
                @error('prompt')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>



            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-dark">Send</button>
            </div>

        </div>
    </form>
</div>
