<style>
    /* ============================================================
       FEATURED VIDEOS — HOME PAGE
    ============================================================ */

    .fv-section {
        background: #f6f8ef;
        padding: 70px 0 80px;
    }

    .fv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 22px;
    }

    .fv-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 26px rgba(61, 84, 22, 0.08);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .fv-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 38px rgba(61, 84, 22, 0.16);
    }

    /* Thumbnail */
    .fv-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #1a1a1a;
        overflow: hidden;
    }

    .fv-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .fv-card:hover .fv-thumb img {
        transform: scale(1.05);
    }

    .fv-thumb-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #3d5416, #83a133);
        color: rgba(255, 255, 255, 0.85);
        font-size: 44px;
    }

    /* Play button */
    .fv-play {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 26px;
        pointer-events: none;
    }

    .fv-play .bi {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(131, 161, 51, 0.92);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.28);
        transition: transform 0.3s ease;
    }

    .fv-card:hover .fv-play .bi {
        transform: scale(1.1);
    }

    /* YouTube badge */
    .fv-source-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 5px 10px;
        border-radius: 20px;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .fv-source-badge .fab {
        color: #ff0000;
        font-size: 13px;
    }

    /* Body */
    .fv-body {
        padding: 18px 20px 22px;
    }

    .fv-title {
        font-size: 17px;
        font-weight: 800;
        color: #1f2b0e;
        margin-bottom: 6px;
        line-height: 1.35;
    }

    .fv-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
    }

    /* ---------------- LIGHTBOX ---------------- */

    .fv-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.94);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 30px;
    }

    .fv-lightbox.open {
        display: flex;
        animation: fvFadeIn 0.2s ease;
    }

    @keyframes fvFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .fv-lightbox-content {
        position: relative;
        width: 100%;
        max-width: 960px;
    }

    .fv-player-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #000;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.55);
    }

    .fv-player-wrap iframe,
    .fv-player-wrap video {
        width: 100%;
        height: 100%;
        border: 0;
        display: block;
    }

    .fv-caption {
        color: #fff;
        text-align: center;
        margin-top: 18px;
        font-size: 14px;
    }

    .fv-caption strong {
        display: block;
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .fv-caption span {
        opacity: 0.85;
    }

    .fv-close {
        position: absolute;
        top: -54px;
        right: 0;
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

    .fv-close:hover {
        background: #83a133;
        border-color: #83a133;
    }

    /* ---------------- MOBILE ---------------- */

    @media (max-width: 768px) {

        .fv-section {
            padding: 45px 0 55px;
        }

        .fv-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .fv-title {
            font-size: 15px;
        }

        .fv-close {
            top: -50px;
            right: 0;
        }
    }
</style>
