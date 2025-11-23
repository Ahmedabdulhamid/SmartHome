 <div class="swiper-wrapper">
            @foreach ($sliders as $slider )
                <div class="swiper-slide" style="background-image: url('{{ asset('storage/' . $slider->path) }}');"
>
              <div class="content">
                <h2><a href="#">{{ $slider->getTranslation('name',app()->getLocale()) }}</a></h2>
                <p>{{ $slider->getTranslation('desc',app()->getLocale()) }}</p>
              </div>
            </div>
            @endforeach




          </div>

          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>

          <div class="swiper-pagination"></div>
