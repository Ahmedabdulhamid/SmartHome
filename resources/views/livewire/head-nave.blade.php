 <div class="cart-icon position-relative me-3">
                <a href="#" class="text-secondary fs-4 mx-2" style="text-decoration: none;"> {{-- Href فارغ --}}
                    <i class="bi bi-bag"></i>
                    @if ($cartCount > 0)

                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle"
                            style="font-size: 0.65em; padding: .3em .5em;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>
