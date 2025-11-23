<footer id="footer" class="footer dark-background">

    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-4 footer-about">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center me-auto me-xl-0">

                    <img src="{{ Storage::url($setting->site_logo) }}" alt="">

                </a>
                <div class="footer-contact pt-3">
                    <p>{{ $setting->site_address }}</p>

                    <p class="mt-3"><strong>{{ __('web.phone') }}:</strong> <span>{{ $setting->site_phone }}</span>
                    </p>
                    <p><strong>{{ __('web.email') }}:</strong> <span>{{ $setting->site_email }}</span></p>
                </div>
                <div class="social-links d-flex mt-4">
                    <a href="{{ $setting->facebook_url }}" target="_blank" class="facebook">
                        <i class="bi bi-facebook"></i>
                    </a>


                    <a href="https://wa.me/{{ $setting->site_phone }}" target="_blank" class="whatsapp"><i
                            class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 footer-links">
                <h4>Useful Links</h4>
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                    <li><a href="{{ route('about.us') }}">{{ __('web.about') }}</a></li>
                    <li><a href="{{ route('contact.us') }}">{{ __('web.contact') }}</a></li>


                </ul>
            </div>

              <div class="col-lg-2 col-md-4 footer-links">
                <h4>{{ __('web.pages') }}</h4>
                <ul>
                    @foreach ($pages as $page)
                        <li><a href="{{ route('pages',$page->slug) }}">{{$page->getTranslation('title',app()->getLocale()) }}</a></li>
                    @endforeach



                </ul>
            </div>


        </div>
    </div>



</footer>
