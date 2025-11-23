<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-6">Product Details</h1>

    <div class="grid grid-cols-3 gap-4">
        @foreach ($record->images as $image)
            <a data-fancybox="gallery"
               data-caption="{{ $record->name['en'] ?? 'Product Image' }}"
               href="{{ asset('storage/'.$image->path) }}">
                <img src="{{ asset('storage/'.$image->path) }}"
                     class="rounded-lg shadow-md w-full h-48 object-cover hover:opacity-80 transition" />
            </a>
        @endforeach
    </div>
</x-filament-panels::page>
