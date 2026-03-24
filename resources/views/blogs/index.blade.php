<!DOCTYPE html>
<html lang="en">
@section('title', __('web.blogs'))
@include('users_layout.head')
@livewireStyles

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl
 @else
 direction:ltr @endif">
    @include('users_layout.header')

    <main class="main my-5">
        <div class="container" data-aos="fade-up" data-aos-delay="100">

            <div class="row g-4 justify-content-center">

                @if (isset($blogs) && count($blogs) > 0)
                    @foreach ($blogs as $blog)
                        <div class="col-lg-4 col-md-6">
                            <article
                                class="blog-card h-100 bg-white shadow-lg border-0 rounded-4 overflow-hidden position-relative transition-all"
                                style="transition: all 0.4s ease-in-out; cursor: pointer;"
                                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 1.5rem 4rem rgba(0,0,0,.25)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.15)';">

                                <div class="card-image-wrapper overflow-hidden" style="height: 250px;">
                                    <a href="{{ route('blogs.show', $blog->slug) }}" class="stretched-link">
                                        @if ($blog->image)
                                            <img src="{{ url('storage/' . $blog->image) }}"
                                                class="card-img-top w-100 h-100 object-fit-cover"
                                                alt="{{ $blog->getTranslation('title', app()->getLocale()) }}"
                                                style="transition: transform 0.5s ease;"
                                                onmouseover="this.style.transform='scale(1.1)'; this.style.opacity='0.9';"
                                                onmouseout="this.style.transform='scale(1)'; this.style.opacity='1';">
                                        @else
                                            <div
                                                class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                                <i class="bi bi-journal-text text-primary fs-1"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>

                                <div class="card-body p-4 d-flex flex-column">

                                    <div class="d-flex align-items-center mb-3 text-muted small">
                                        <span class="me-3"><i class="bi bi-calendar me-1"></i>
                                            {{ $blog->created_at->format('M d, Y') }}</span>
                                        @if ($blog->category)
                                            <span class="badge bg-primary-subtle text-primary fw-normal">
                                                <i class="bi bi-tag me-1"></i>
                                                {{ $blog->category->getTranslation('name', app()->getLocale()) }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="card-title h5 mb-3 text-dark fw-bold">
                                        <a href="{{ route('blogs.show', $blog->slug) }}"
                                            class="text-decoration-none text-dark hover-primary">
                                            {{ $blog->getTranslation('title', app()->getLocale()) }}
                                        </a>
                                    </h3>

                                    <p class="card-text text-secondary mb-4 flex-grow-1">
                                        {!! Str::limit($blog->getTranslation('excerpt', app()->getLocale()), 120) !!}
                                    </p>

                                    <div class="d-flex align-items-center pt-2 border-top">
                                        <div class="author-info">
                                            <small class="text-muted d-block">{{ __('web.written_by') }}</small>
                                            @if ($blog->author)
                                                <span
                                                    class="fw-bold text-dark">{{ $blog->author->name ?? __('web.unknown_author') }}</span>
                                            @else
                                                <span class="fw-bold text-dark">{{ __('web.admin') }}</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('blogs.show', $blog->slug) }}"
                                            class="btn btn-sm btn-outline-primary ms-auto">
                                            {{ __('web.read_more') }}
                                        </a>
                                    </div>

                                </div>
                            </article>
                        </div>
                    @endforeach
                    {{ $blogs->links('vendor.pagination.bootstrap-5') }}
                @else
                    <div class="col-12 text-center py-5">
                        <p class="lead text-muted">{{ __('web.no_articles_found') }}</p>
                    </div>
                @endif

            </div>
        </div>



    </main>
    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <div id="preloader"></div>

    @include('users_layout.script')
    @livewireScripts
</body>

</html>
