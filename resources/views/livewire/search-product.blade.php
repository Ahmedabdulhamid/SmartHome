<div class="input-group position-relative">

    <input type="search" wire:model.live="item" class="form-control form-control-lg"
        placeholder="{{ __('web.search_placeholder') ?? 'ابحث عن منتجات...' }}">

    @if (!empty($products))
        <div class="list-group position-absolute w-100 mt-5 shadow top-0" style="z-index: 999;">
            @foreach ($products as $product)

                <a href="{{ route('product.details', $product->slug) }}" class="list-group-item list-group-item-action">
                    {{ $product->getTranslation('name', app()->getLocale()) }}
                </a>
            @endforeach
        </div>
    @endif

</div>
