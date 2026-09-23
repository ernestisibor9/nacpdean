{{-- ============================================================
     NACPDEAN ID CARD STYLES (SHARED PARTIAL)
============================================================ --}}
<style>
    .nacp-generated-id-card {
        width: 100%; max-width: 520px; border-radius: 16px; overflow: hidden;
        background: #ffffff; border: 1px solid #d1d5db;
        box-shadow: 0 12px 30px rgba(0,0,0,.16);
    }

    .nacp-id-header {
        display: flex; align-items: center; gap: 13px;
        padding: 15px 18px;
        background: linear-gradient(135deg, #064e3b, #047857, #059669);
        color: #fff;
    }

    .nacp-id-logo {
        width: 45px; height: 45px; border-radius: 50%;
        background: rgba(255,255,255,.15);
        display: flex; align-items: center; justify-content: center; font-size: 21px;
    }

    .nacp-id-organization { font-size: 18px; font-weight: 900; letter-spacing: .5px; }
    .nacp-id-subtitle { font-size: 9px; font-weight: 700; letter-spacing: 1px; opacity: .85; margin-top: 2px; }

    .nacp-id-body { display: flex; gap: 18px; padding: 20px; background: #fff; }

    .nacp-id-photo {
        width: 100px; min-width: 100px; height: 120px;
        overflow: hidden; border-radius: 8px;
        border: 2px solid #047857; background: #f3f4f6;
    }

    .nacp-id-photo img { width: 100%; height: 100%; object-fit: cover; }

    .nacp-id-photo-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        color: #9ca3af; font-size: 35px;
    }

    .nacp-id-information { flex: 1; min-width: 0; }

    .nacp-id-name {
        color: #111827; font-size: 18px; font-weight: 900;
        text-transform: uppercase; line-height: 1.25; margin-bottom: 3px;
    }

    .nacp-id-category {
        color: #047857; font-size: 11px; font-weight: 800;
        text-transform: uppercase; margin-bottom: 11px;
    }

    .nacp-id-detail {
        display: flex; justify-content: space-between; gap: 10px;
        padding: 5px 0; border-bottom: 1px solid #f3f4f6;
    }

    .nacp-id-detail span { color: #6b7280; font-size: 10px; }
    .nacp-id-detail strong { color: #374151; font-size: 10px; text-align: right; }

    .nacp-id-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 18px; background: #f0fdf4;
        border-top: 1px solid #dcfce7; color: #047857;
        font-size: 9px; font-weight: 800; letter-spacing: .5px;
    }

    /* UNPAID OVERLAY */
    .id-card-unpaid { position: relative; overflow: hidden; }
    .id-card-unpaid::after {
        content: ""; position: absolute; left: 50%; top: 50%;
        width: 85%; height: 85%;
        transform: translate(-50%, -50%) rotate(-12deg);
        border: 14px solid #dc2626; border-radius: 18px;
        opacity: .92; pointer-events: none; z-index: 20;
    }
    .id-card-unpaid::before {
        content: "×"; position: absolute; left: 50%; top: 50%;
        transform: translate(-50%, -50%) rotate(-12deg);
        color: #dc2626; font-size: 230px; line-height: 1; font-weight: 900;
        opacity: .92; pointer-events: none; z-index: 21;
        text-shadow: 0 2px 3px rgba(0,0,0,.15);
    }
    .id-card-unpaid-label {
        position: absolute; left: 50%; top: 50%;
        transform: translate(-50%, -50%) rotate(-12deg);
        z-index: 22; background: #dc2626; color: #fff;
        padding: 8px 18px; border-radius: 8px;
        font-size: 15px; font-weight: 900; letter-spacing: 1px;
        text-transform: uppercase; white-space: nowrap;
        box-shadow: 0 5px 15px rgba(0,0,0,.2); pointer-events: none;
    }

    /* EXPIRED OVERLAY */
    .id-card-expired { position: relative; overflow: hidden; }
    .id-card-expired::after {
        content: ""; position: absolute; left: 50%; top: 50%;
        width: 88%; height: 88%;
        transform: translate(-50%, -50%) rotate(-12deg);
        border: 14px solid #dc2626; border-radius: 18px;
        opacity: .95; pointer-events: none; z-index: 20;
    }
    .id-card-expired::before {
        content: "×"; position: absolute; left: 50%; top: 50%;
        transform: translate(-50%, -50%) rotate(-12deg);
        color: #dc2626; font-size: 240px; line-height: 1; font-weight: 900;
        opacity: .92; pointer-events: none; z-index: 21;
        text-shadow: 0 2px 3px rgba(0,0,0,.18);
    }
    .id-card-expired-label {
        position: absolute; left: 50%; top: 50%;
        transform: translate(-50%, -50%) rotate(-12deg);
        z-index: 22; background: #dc2626; color: #fff;
        padding: 9px 20px; border-radius: 8px;
        font-size: 15px; font-weight: 900; letter-spacing: 1px;
        text-transform: uppercase; white-space: nowrap;
        box-shadow: 0 5px 15px rgba(0,0,0,.22); pointer-events: none;
    }

    /* MOBILE */
    @media (max-width: 576px) {
        .nacp-id-body { padding: 15px; gap: 12px; }
        .nacp-id-photo { width: 80px; min-width: 80px; height: 100px; }
        .nacp-id-name { font-size: 15px; }
        .nacp-id-category { font-size: 9px; }
        .nacp-id-detail { padding: 4px 0; }
        .nacp-id-detail span, .nacp-id-detail strong { font-size: 9px; }
        .id-card-unpaid::after, .id-card-expired::after { width: 88%; height: 84%; border-width: 10px; }
        .id-card-unpaid::before, .id-card-expired::before { font-size: 170px; }
        .id-card-unpaid-label, .id-card-expired-label { font-size: 11px; padding: 7px 12px; letter-spacing: .7px; }
    }
</style>
