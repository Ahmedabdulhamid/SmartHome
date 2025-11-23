<style>
    .download-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .download-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .download-card .badge {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
</style>

<section id="downloads-section" class="downloads-section section">
    <div class="container" data-aos="fade-up">

        <!-- Section Title -->
        <div class="section-title d-flex align-items-center justify-content-between mb-4">
            <h2>{{ __('web.downloads') }}</h2>

        </div>

        <div class="row g-4">

            @foreach ($downloads as $download)
                <div class="col-lg-4 col-md-6">
                    <div
                        class="download-card p-3 border rounded text-center shadow-sm h-100 d-flex flex-column justify-content-between">

                        <!-- نوع الملف -->


                        <!-- عنوان الملف -->
                        <h4 class="mb-3">{{ $download->getTranslation('title', app()->getLocale()) }}</h4>
                        <a href="{{ asset('storage/' . $download->file_path) }}" target="_blank" class="btn btn-primary mt-auto my-2">
                            {{ __('web.preview') }}
                        </a>

                        <!-- زر التحميل -->
                        <a href="{{ asset('storage/' . $download->file_path) }}" class="btn btn-success mt-auto"
                            download>
                            <i class="bi bi-download me-1"></i> {{ __('web.download') }}
                        </a>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section>
