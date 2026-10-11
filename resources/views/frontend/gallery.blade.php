@extends('frontend.master')

@section('home')

<style>
    /* ================================================================
       NACPDEAN PUBLIC GALLERY
       Uses the site's olive/lime brand palette.
    ================================================================ */

    :root {
        --brand-olive: #83a133;
        --brand-lime:  #a4b83f;
        --brand-dark:  #5d7a20;
        --brand-deep:  #3d5416;
        --ink:         #1f2b0e;
        --muted:       #6b7280;
        --soft:        #f6f8ef;
    }

    /* ---------------- HERO ---------------- */

    .glr-hero {
        background: linear-gradient(135deg,
                    var(--brand-deep) 0%,
                    var(--brand-dark) 45%,
                    var(--brand-olive) 100%);
        color: #fff;
        padding: 60px 0 55px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .glr-hero::before {
        content: "";
        position: absolute;
        width: 460px;
        height: 460px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.06);
        top: -230px;
        right: -150px;
    }

    .glr-hero::after {
        content: "";
        position: absolute;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        bottom: -200px;
        left: -120px;
    }

    .glr-hero-content {
        position: relative;
        z-index: 2;
    }

    .glr-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 15px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.8px;
        margin-bottom: 16px;
        text-transform: uppercase;
    }

    .glr-hero h1 {
        font-size: 38px;
        font-weight: 800;
        letter-spacing: -0.8px;
        margin-bottom: 10px;
        line-height: 1.15;
    }

    .glr-hero p {
        font-size: 15px;
        color: rgba(255, 255, 255, 0.9);
        max-width: 640px;
        margin: 0 auto;
        line-height: 1.65;
    }

    /* ---------------- GALLERY BODY ---------------- */

    .glr-section {
        background: var(--soft);
        padding: 55px 0 80px;
    }

    .glr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 18px;
    }

    .glr-item {
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(61, 84, 22, 0.08);
        cursor: pointer;
        transition: all 0.35s ease;
        display: block;
    }

    .glr-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(61, 84, 22, 0.16);
    }

    .glr-item-media {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #eef2e0;
    }

    /* Varied heights for a richer masonry feel */
    .glr-item:nth-child(3n+1) .glr-item-media {
        aspect-ratio: 4 / 5;
    }

    .glr-item:nth-child(5n+2) .glr-item-media {
        aspect-ratio: 1 / 1;
    }

    .glr-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .glr-item:hover img {
        transform: scale(1.06);
    }

    .glr-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            to top,
            rgba(31, 43, 14, 0.85) 0%,
            rgba(31, 43, 14, 0.35) 45%,
            rgba(31, 43, 14, 0) 100%
        );
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 20px 18px;
        color: #fff;
    }

    .glr-item:hover .glr-overlay {
        opacity: 1;
    }

    .glr-overlay-title {
        font-size: 15px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 3px;
    }

    .glr-overlay-caption {
        font-size: 12px;
        line-height: 1.5;
        opacity: 0.9;
    }

    .glr-zoom {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        color: var(--brand-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        opacity: 0;
        transform: translateY(-6px);
        transition: all 0.3s ease;
    }

    .glr-item:hover .glr-zoom {
        opacity: 1;
        transform: translateY(0);
    }

    /* ---------------- PAGINATION ---------------- */

    .glr-pagination {
        display: flex;
        justify-content: center;
        margin-top: 55px;
    }

    .glr-pagination svg {
        width: 16px;
        height: 16px;
    }

    /*
    |--------------------------------------------------------------------------
    | Style Laravel's pagination markup in the brand olive palette.
    |--------------------------------------------------------------------------
    */

    .glr-pagination .pagination {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        justify-content: center;
    }

    .glr-pagination .page-item {
        list-style: none;
    }

    .glr-pagination .page-item .page-link,
    .glr-pagination .page-item > span,
    .glr-pagination .page-item > a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border-radius: 10px;
        background: #ffffff;
        color: #3d5416;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #e2e8d6;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .glr-pagination .page-item > a:hover {
        background: var(--brand-olive);
        color: #ffffff;
        border-color: var(--brand-olive);
    }

    .glr-pagination .page-item.active .page-link,
    .glr-pagination .page-item.active > span,
    .glr-pagination .page-item.active > a {
        background: var(--brand-olive);
        color: #ffffff;
        border-color: var(--brand-olive);
    }

    .glr-pagination .page-item.disabled .page-link,
    .glr-pagination .page-item.disabled > span {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Hide the "Showing X to Y of Z results" text if present */
    .glr-pagination > div:first-child {
        display: none;
    }

    /* ---------------- LIGHTBOX ---------------- */

    .glr-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(15, 20, 6, 0.94);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .glr-lightbox.open {
        display: flex;
        animation: glrFadeIn 0.2s ease;
    }

    @keyframes glrFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .glr-lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
    }

    .glr-lightbox-content img {
        max-width: 90vw;
        max-height: 78vh;
        border-radius: 12px;
        display: block;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.55);
    }

    .glr-lightbox-caption {
        color: #fff;
        text-align: center;
        margin-top: 16px;
        font-size: 14px;
    }

    .glr-lightbox-caption strong {
        display: block;
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .glr-lightbox-close,
    .glr-lightbox-nav {
        position: absolute;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .glr-lightbox-close:hover,
    .glr-lightbox-nav:hover {
        background: var(--brand-olive);
        border-color: var(--brand-olive);
    }

    .glr-lightbox-close {
        top: -58px;
        right: 0;
    }

    .glr-lightbox-nav.prev {
        top: 50%;
        left: -66px;
        transform: translateY(-50%);
    }

    .glr-lightbox-nav.next {
        top: 50%;
        right: -66px;
        transform: translateY(-50%);
    }

    /* ---------------- EMPTY ---------------- */

    .glr-empty {
        text-align: center;
        padding: 80px 20px;
        color: var(--muted);
    }

    .glr-empty i {
        font-size: 54px;
        color: #c9d3b0;
        margin-bottom: 14px;
    }

    /* ---------------- MOBILE ---------------- */

    @media (max-width: 768px) {

        .glr-hero {
            padding: 40px 0 35px;
        }

        .glr-hero h1 {
            font-size: 26px;
        }

        .glr-hero p {
            font-size: 13px;
        }

        .glr-section {
            padding: 35px 0 55px;
        }

        .glr-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }

        .glr-item:nth-child(3n+1) .glr-item-media {
            aspect-ratio: 1 / 1;
        }

        .glr-lightbox-nav.prev {
            left: 10px;
        }

        .glr-lightbox-nav.next {
            right: 10px;
        }

        .glr-lightbox-close {
            top: -52px;
            right: 0;
        }

        .glr-pagination .page-item .page-link,
        .glr-pagination .page-item > span,
        .glr-pagination .page-item > a {
            min-width: 36px;
            height: 36px;
            font-size: 13px;
            padding: 0 10px;
        }
    }
</style>

{{-- ============================================================
     HERO (compact)
============================================================ --}}
<section class="glr-hero">
    <div class="container">
        <div class="glr-hero-content">

            <div class="glr-badge">
                <i class="fas fa-camera"></i>
                NACPDEAN GALLERY
            </div>

            <h1>Moments &amp; Milestones</h1>

            <p>
                A visual journey through NACPDEAN's conferences, events,
                members and stakeholder engagements across Nigeria.
            </p>

        </div>
    </div>
</section>


{{-- ============================================================
     GALLERY GRID
============================================================ --}}
<section class="glr-section">
    <div class="container">

        @if ($photos->isEmpty())

            <div class="glr-empty">
                <i class="fas fa-images d-block"></i>
                <h4 class="fw-bold mb-2">No photos yet</h4>
                <p class="mb-0">The gallery will appear here once photos are uploaded.</p>
            </div>

        @else

            <div class="glr-grid">
                @foreach ($photos as $index => $photo)

                    <div class="glr-item"
                         data-index="{{ $index }}"
                         data-src="{{ $photo->imageUrl() }}"
                         data-title="{{ $photo->title }}"
                         data-caption="{{ $photo->caption }}">

                        <div class="glr-item-media">
                            <img src="{{ $photo->imageUrl() }}"
                                 alt="{{ $photo->title ?: 'Gallery photo' }}"
                                 loading="lazy">

                            <div class="glr-overlay">
                                @if ($photo->title)
                                    <div class="glr-overlay-title">{{ $photo->title }}</div>
                                @endif
                                @if ($photo->caption)
                                    <div class="glr-overlay-caption">{{ $photo->caption }}</div>
                                @endif
                            </div>

                            <div class="glr-zoom">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>

                    </div>

                @endforeach
            </div>

            {{-- PAGINATION --}}
            @if ($photos->hasPages())
                <div class="glr-pagination">
                    {{ $photos->links() }}
                </div>
            @endif

        @endif

    </div>
</section>


{{-- ============================================================
     LIGHTBOX
============================================================ --}}
<div class="glr-lightbox" id="glrLightbox">

    <div class="glr-lightbox-content">

        <button type="button" class="glr-lightbox-close" id="glrClose">
            <i class="fas fa-times"></i>
        </button>

        <button type="button" class="glr-lightbox-nav prev" id="glrPrev">
            <i class="fas fa-chevron-left"></i>
        </button>

        <img src="" alt="" id="glrImage">

        <div class="glr-lightbox-caption">
            <strong id="glrTitle"></strong>
            <span id="glrCaption"></span>
        </div>

        <button type="button" class="glr-lightbox-nav next" id="glrNext">
            <i class="fas fa-chevron-right"></i>
        </button>

    </div>

</div>


{{-- ============================================================
     LIGHTBOX SCRIPT
============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const items = Array.from(document.querySelectorAll('.glr-item'));

    if (!items.length) return;

    const lightbox  = document.getElementById('glrLightbox');
    const img       = document.getElementById('glrImage');
    const titleEl   = document.getElementById('glrTitle');
    const captionEl = document.getElementById('glrCaption');
    const closeBtn  = document.getElementById('glrClose');
    const prevBtn   = document.getElementById('glrPrev');
    const nextBtn   = document.getElementById('glrNext');

    let current = 0;

    function open(index) {
        current = index;

        const el = items[current];

        img.src = el.dataset.src;
        img.alt = el.dataset.title || 'Gallery photo';

        titleEl.textContent   = el.dataset.title || '';
        captionEl.textContent = el.dataset.caption || '';

        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
    }

    function next() {
        current = (current + 1) % items.length;
        open(current);
    }

    function prev() {
        current = (current - 1 + items.length) % items.length;
        open(current);
    }

    items.forEach((el, i) => {
        el.addEventListener('click', () => open(i));
    });

    closeBtn.addEventListener('click', close);
    nextBtn.addEventListener('click', next);
    prevBtn.addEventListener('click', prev);

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) close();
    });

    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('open')) return;

        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowRight') next();
        if (e.key === 'ArrowLeft')  prev();
    });
});
</script>

@endsection
