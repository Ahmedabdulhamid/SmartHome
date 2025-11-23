<!DOCTYPE html>
<html lang="en">
@section('title', __('web.contact'))
@include('users_layout.head')

<body class="index-page" style="@if (app()->getLocale() == 'ar') direction:rtl @else direction:ltr @endif">
    @include('users_layout.header')

   <main class="main">

    <div class="page-title py-5 bg-light">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0 fw-bold text-dark">{{ $blog->getTranslation('title', app()->getLocale()) }}</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('home') }}">{{ __('web.home') }}</a></li>
                    <li><a href="{{ route('blogs') }}">{{ __('web.blog') }}</a></li>
                    <li class="current">{{ $blog->getTranslation('title', app()->getLocale()) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="blog-details" class="blog-details section py-5">
        <div class="container">
            <div class="row">

                <div class="col-lg-8">
                    <article class="blog-post">

                        @if ($blog->featured_image)
                            <div class="post-img mb-4 rounded-4 overflow-hidden shadow-sm">
                                <img src="{{ url('public/storage/' . $blog->featured_image) }}" class="img-fluid w-100 object-fit-cover" alt="{{ $blog->getTranslation('title', app()->getLocale()) }}" style="max-height: 500px;">
                            </div>
                        @elseif($blog->video_url)
                             <div class="post-video mb-4 rounded-4 overflow-hidden shadow-sm ratio ratio-16x9">
                                <iframe src="{{ $blog->video_url }}" allowfullscreen></iframe>
                            </div>
                        @endif

                        <div class="post-meta d-flex flex-wrap align-items-center mb-4 text-muted small border-bottom pb-3">
                            <span class="me-4"><i class="bi bi-person me-1"></i>
                                {{ $blog->author->name ?? __('web.admin') }}
                            </span>
                            <span class="me-4"><i class="bi bi-clock me-1"></i>
                                {{ $blog->published_at ? $blog->published_at->format('M d, Y') : $blog->created_at->format('M d, Y') }}
                            </span>
                            @if ($blog->category)
                                <span class="me-4"><i class="bi bi-folder me-1"></i>
                                    <a href="#" class="text-primary text-decoration-none">
                                        {{ $blog->category->getTranslation('name', app()->getLocale()) }}
                                    </a>
                                </span>
                            @endif
                            <span><i class="bi bi-eye me-1"></i> {{ number_format($blog->views_count) }} {{ __('web.views') }}</span>
                        </div>

                        <div class="post-content fs-5 text-dark mb-5 blog-content">
                            {!! $blog->getTranslation('content', app()->getLocale()) !!}
                        </div>
                        @if ($blog->children->count() > 0)
                            <div class="sub-sections mt-5 border-top pt-4">
                                <h4 class="fw-bold mb-3">{{ __('web.related_topics') }}</h4>
                                <ul class="list-group list-group-flush">
                                    @foreach ($blog->children as $child)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ route('blogs.show', $child->slug) }}" class="text-primary text-decoration-none fw-medium">
                                                {{ $child->getTranslation('title', app()->getLocale()) }}
                                            </a>
                                            <i class="bi bi-arrow-right"></i>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </article>
                </div>

                <div class="col-lg-4 sidebar">

                    <div class="sidebar-box author-box bg-light p-4 rounded-4 mb-4 shadow-sm">
                        <h4 class="fw-bold mb-3 border-bottom pb-2">{{ __('web.about_the_author') }}</h4>
                        @if ($blog->author)
                            <div class="d-flex align-items-center">
                                {{-- <img src="{{ url('public/storage/' . $blog->author->profile_photo) }}" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;"> --}}
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $blog->author->name ?? __('web.unknown_author') }}</h5>
                                    <small class="text-muted">{{ __('web.admin_role') }}</small>
                                </div>
                            </div>
                            @else
                            <p class="text-muted">{{ __('web.post_by_admin') }}</p>
                        @endif
                    </div>

                    {{-- @if(isset($recent_blogs) && $recent_blogs->count() > 0)
                        <div class="sidebar-box recent-posts mb-4">
                            <h4 class="fw-bold mb-3 border-bottom pb-2">{{ __('web.recent_posts') }}</h4>
                            <ul class="list-unstyled">
                                @foreach($recent_blogs as $post)
                                    <li class="d-flex mb-3">
                                        <a href="{{ route('blogs.show', $post->slug) }}" class="text-decoration-none">
                                            <h6 class="mb-1 text-dark">{{ $post->getTranslation('title', app()->getLocale()) }}</h6>
                                            <small class="text-muted"><i class="bi bi-clock"></i> {{ $post->created_at->format('M d, Y') }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif --}}

                    <div class="sidebar-box meta-info bg-light p-4 rounded-4 shadow-sm">
                        <h4 class="fw-bold mb-3 border-bottom pb-2">{{ __('web.seo_details') }}</h4>
                        <p class="small text-muted mb-0">
                            **{{ __('web.meta_description') }}:** <br>
                            {{ $blog->getTranslation('meta_description', app()->getLocale()) ?? __('web.not_available') }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>
    @include('users_layout.footer')

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>
    @include('users_layout.script')
</body>



</html>
