<form  wire:submit.prevent="update">

    <div class="mb-3">
        <label class="form-label">Client Name</label>
        <input type="text" class="form-control" placeholder="Enter client name" wire:model="name"
           >
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Client Email</label>
        <input type="email" class="form-control" placeholder="Enter client email" wire:model="email"
            >
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Client Phone</label>
        <input type="text" class="form-control" placeholder="Enter client phone" wire:model="phone"
            >
        @error('phone')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Client Address</label>
        <input type="text" class="form-control" placeholder="Enter client address" wire:model="address"
            >
        @error('address')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>


    <button class="btn btn-primary w-100 mb-5" type="submit">Update Client</button>



</form>
<script>
    window.addEventListener('clientUpdated', event => {
        console.log(event.detail.message);


        toastr.success(event.detail[0].message);
    });
</script>
