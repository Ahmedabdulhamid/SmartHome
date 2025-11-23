<form wire:submit.prevent="submit" class="php-email-form">
    <div class="row gy-4">

        <div class="col-md-6">
            <input type="text" wire:model='name' class="form-control" placeholder="{{ __('web.your_name') }}">
            @error('name') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-6">
            <input type="email" wire:model='email' class="form-control" placeholder="{{ __('web.your_email') }}">
            @error('email') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-12">
            <input type="text" wire:model='subject' class="form-control" placeholder="{{ __('web.subject') }}">
            @error('subject') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-12">
            <textarea wire:model='message' class="form-control" rows="6" placeholder="{{ __('web.message') }}"></textarea>
            @error('message') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-12 text-center">
            <button type="submit">{{ __('web.send_message') }}</button>
        </div>

    </div>
</form>

<script>
    window.addEventListener('success', event => {
        console.log(event);

        toastr.success(event.detail);
    });
    window.addEventListener('error', event => {
        console.log(event);

        toastr.error(event.detail.message);
    });
</script>
