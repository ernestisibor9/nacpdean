@extends('member.member_dashboard')

@section('title', 'NACPDEAN - Member Dashboard')

@section('member')


    <style>
        /* =========================================================
           NACPDEAN MEMBER DASHBOARD
        ========================================================= */

        .nacp-dashboard {
            min-height: calc(100vh - 60px);
            background: #f5f7f6;
            padding: 24px 0 50px;
        }

        .nacp-dashboard * {
            box-sizing: border-box;
        }

        /* =========================================================
           HERO
        ========================================================= */

        .nacp-hero {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, #064e3b 0%, #047857 55%, #059669 100%);
            border-radius: 20px;
            padding: 30px 32px;
            margin-bottom: 24px;
            color: #fff;
            box-shadow: 0 12px 35px rgba(6, 78, 59, .16);
        }

        .nacp-hero::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .055);
            right: -100px;
            top: -170px;
        }

        .nacp-hero::after {
            content: "";
            position: absolute;
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .045);
            right: 180px;
            bottom: -150px;
        }

        .nacp-hero-content {
            position: relative;
            z-index: 2;
        }

        .nacp-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-radius: 30px;
            background: rgba(255, 255, 255, .11);
            border: 1px solid rgba(255, 255, 255, .13);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .4px;
            margin-bottom: 14px;
        }

        .nacp-hero h1 {
            margin: 0 0 7px;
            font-size: 29px;
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -.4px;
        }

        .nacp-hero p {
            margin: 0;
            color: rgba(255, 255, 255, .82);
            font-size: 15px;
            line-height: 1.6;
        }

        /* =========================================================
           ALERT
        ========================================================= */

        .nacp-alert {
            border: 0;
            border-radius: 13px;
            padding: 16px 19px;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .035);
            font-size: 14px;
            line-height: 1.6;
        }

        /* =========================================================
           PAYMENT REQUIRED
        ========================================================= */

        .payment-required {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .055);
        }

        .payment-required-header {
            padding: 28px 30px;
            background: linear-gradient(135deg,
                    #ecfdf5 0%,
                    #f0fdf4 100%);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .payment-required-icon {
            width: 64px;
            height: 64px;
            min-width: 64px;
            border-radius: 17px;
            background: #047857;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
            box-shadow: 0 8px 20px rgba(4, 120, 87, .18);
        }

        .payment-required-header h3 {
            margin: 0 0 7px;
            font-size: 21px;
            font-weight: 800;
            color: #111827;
        }

        .payment-required-header p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        .payment-required-body {
            padding: 28px 30px;
        }

        .payment-step {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 19px;
        }

        .payment-step:last-child {
            margin-bottom: 0;
        }

        .payment-step-number {
            width: 31px;
            height: 31px;
            min-width: 31px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #047857;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .payment-step strong {
            display: block;
            color: #374151;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .payment-step span {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
        }

        .payment-action {
            border-top: 1px solid #f0f0f0;
            margin-top: 25px;
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .payment-action strong {
            display: block;
            color: #111827;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .payment-action span {
            color: #6b7280;
            font-size: 12px;
        }

        /* =========================================================
           BUTTONS
        ========================================================= */

        .nacp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 10px;
            padding: 12px 18px;
            background: #047857;
            color: #fff !important;
            text-decoration: none !important;
            font-size: 13px;
            font-weight: 700;
            transition: all .2s ease;
            box-shadow: 0 5px 14px rgba(4, 120, 87, .16);
        }

        .nacp-btn:hover {
            background: #065f46;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(4, 120, 87, .22);
        }

        .nacp-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 10px 15px;
            background: #fff;
            color: #374151 !important;
            text-decoration: none !important;
            font-size: 12px;
            font-weight: 700;
            transition: all .2s ease;
        }

        .nacp-btn-outline:hover {
            border-color: #047857;
            color: #047857 !important;
            background: #f0fdf4;
        }

        /* =========================================================
           STAT CARDS
        ========================================================= */

        .nacp-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 17px;
            padding: 21px;
            height: 100%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .035);
        }

        .nacp-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 17px;
        }

        .nacp-stat-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .35px;
        }

        .nacp-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .nacp-stat-icon.green {
            background: #ecfdf5;
            color: #047857;
        }

        .nacp-stat-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .nacp-stat-icon.orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .nacp-stat-icon.purple {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .nacp-stat-value {
            color: #111827;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .nacp-stat-description {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
            line-height: 1.6;
        }

        /* =========================================================
           GENERATED NACPDEAN ID CARD
        ========================================================= */

        .nacp-generated-id-card {
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #d1d5db;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .16);
        }

        .nacp-id-header {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 15px 18px;
            background: linear-gradient(135deg,
                    #064e3b,
                    #047857,
                    #059669);
            color: #fff;
        }

        .nacp-id-logo {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .nacp-id-organization {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .5px;
        }

        .nacp-id-subtitle {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            opacity: .85;
            margin-top: 2px;
        }

        .nacp-id-body {
            display: flex;
            gap: 18px;
            padding: 20px;
            background: #fff;
        }

        .nacp-id-photo {
            width: 100px;
            min-width: 100px;
            height: 120px;
            overflow: hidden;
            border-radius: 8px;
            border: 2px solid #047857;
            background: #f3f4f6;
        }

        .nacp-id-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nacp-id-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 35px;
        }

        .nacp-id-information {
            flex: 1;
            min-width: 0;
        }

        .nacp-id-name {
            color: #111827;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.25;
            margin-bottom: 3px;
        }

        .nacp-id-category {
            color: #047857;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 11px;
        }

        .nacp-id-detail {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .nacp-id-detail span {
            color: #6b7280;
            font-size: 10px;
        }

        .nacp-id-detail strong {
            color: #374151;
            font-size: 10px;
            text-align: right;
        }

        .nacp-id-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            background: #f0fdf4;
            border-top: 1px solid #dcfce7;
            color: #047857;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .5px;
        }

        /* =========================================================
           MOBILE ID CARD
        ========================================================= */

        @media (max-width: 576px) {

            .nacp-id-body {
                padding: 15px;
                gap: 12px;
            }

            .nacp-id-photo {
                width: 80px;
                min-width: 80px;
                height: 100px;
            }

            .nacp-id-name {
                font-size: 15px;
            }

            .nacp-id-category {
                font-size: 9px;
            }

            .nacp-id-detail {
                padding: 4px 0;
            }

            .nacp-id-detail span,
            .nacp-id-detail strong {
                font-size: 9px;
            }
        }

        /* =========================================================
           MAIN CARDS
        ========================================================= */

        .nacp-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            height: 100%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .035);
            overflow: hidden;
        }

        .nacp-card-header {
            padding: 20px 22px;
            border-bottom: 1px solid #f0f1f2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .nacp-card-title {
            margin: 0;
            color: #111827;
            font-size: 18px;
            font-weight: 800;
        }

        .nacp-card-subtitle {
            color: #6b7280;
            font-size: 13px;
            margin: 4px 0 0;
            line-height: 1.5;
        }

        .nacp-card-header-icon {
            width: 41px;
            height: 41px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ecfdf5;
            color: #047857;
            font-size: 17px;
        }

        .nacp-card-body {
            padding: 22px;
        }

        /* =========================================================
           MEMBER PROFILE
        ========================================================= */

        .member-profile {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #f0f1f2;
        }

        .member-photo {
            width: 76px;
            height: 92px;
            object-fit: cover;
            border-radius: 11px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .member-photo-placeholder {
            width: 76px;
            height: 92px;
            min-width: 76px;
            border-radius: 11px;
            background: #ecfdf5;
            color: #047857;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .member-name {
            color: #111827;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .member-type {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }

        /* =========================================================
           STATUS BADGES
        ========================================================= */

        .nacp-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .nacp-status.active,
        .nacp-status.paid {
            background: #ecfdf5;
            color: #047857;
        }

        .nacp-status.pending {
            background: #fffbeb;
            color: #b45309;
        }

        .nacp-status.outstanding,
        .nacp-status.expired {
            background: #fef2f2;
            color: #dc2626;
        }

        .nacp-status.rejected {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* =========================================================
           DETAILS
        ========================================================= */

        .nacp-detail {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .nacp-detail:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .nacp-detail-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        .nacp-detail-value {
            color: #374151;
            font-size: 14px;
            font-weight: 700;
            text-align: right;
        }

        /* =========================================================
           PAYMENT CARD
        ========================================================= */

        .payment-summary {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-summary-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .payment-summary h5 {
            color: #111827;
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 5px;
        }

        .payment-summary p {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
            line-height: 1.6;
        }

        /* =========================================================
           MEMBERSHIP
        ========================================================= */

        .membership-highlight {
            padding: 17px;
            border-radius: 12px;
            background: #f8faf9;
            border: 1px solid #edf0ee;
            margin-bottom: 16px;
        }

        .membership-highlight-label {
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 5px;
        }

        .membership-highlight-value {
            color: #111827;
            font-size: 19px;
            font-weight: 800;
        }

        /* =========================================================
           QUICK ACTIONS
        ========================================================= */

        .quick-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none !important;
        }

        .quick-action:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .quick-action:first-child {
            padding-top: 0;
        }

        .quick-action-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quick-action-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #4b5563;
            font-size: 15px;
        }

        .quick-action-title {
            color: #374151;
            font-size: 13px;
            font-weight: 700;
            display: block;
        }

        .quick-action-description {
            color: #6b7280;
            font-size: 12px;
            display: block;
            margin-top: 3px;
            line-height: 1.5;
        }

        .quick-action-arrow {
            color: #9ca3af;
            font-size: 12px;
        }

        /* =========================================================
           ID CARD PREVIEW
        ========================================================= */

        .id-card-preview {
            width: 100%;
            background: #f8faf9;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .nacp-id-card {
            display: block;
            width: 100%;
            max-width: 520px;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .12);
        }

        .id-card-actions {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #f0f1f2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .id-card-actions strong {
            display: block;
            color: #111827;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .id-card-actions span {
            display: block;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }

        /* =========================================================
           UNPAID MEMBERSHIP ID CARD
           EXISTING RED X / CANCEL OVERLAY
        ========================================================= */

        .id-card-unpaid {
            position: relative;
            overflow: hidden;
        }

        .id-card-unpaid::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            width: 85%;
            height: 85%;
            transform: translate(-50%, -50%) rotate(-12deg);
            border: 14px solid #dc2626;
            border-radius: 18px;
            opacity: .92;
            pointer-events: none;
            z-index: 20;
        }

        .id-card-unpaid::before {
            content: "×";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-12deg);
            color: #dc2626;
            font-size: 230px;
            line-height: 1;
            font-weight: 900;
            opacity: .92;
            pointer-events: none;
            z-index: 21;
            text-shadow:
                0 2px 3px rgba(0, 0, 0, .15);
        }

        .id-card-unpaid-label {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-12deg);
            z-index: 22;
            background: #dc2626;
            color: #fff;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            white-space: nowrap;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .2);
            pointer-events: none;
        }

        /* =========================================================
           EXPIRED / INACTIVE MEMBERSHIP ID CARD
           RED X / CANCEL OVERLAY
        ========================================================= */

        .id-card-expired {
            position: relative;
            overflow: hidden;
        }

        .id-card-expired::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            width: 88%;
            height: 88%;
            transform: translate(-50%, -50%) rotate(-12deg);
            border: 14px solid #dc2626;
            border-radius: 18px;
            opacity: .95;
            pointer-events: none;
            z-index: 20;
        }

        .id-card-expired::before {
            content: "×";
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-12deg);
            color: #dc2626;
            font-size: 240px;
            line-height: 1;
            font-weight: 900;
            opacity: .92;
            pointer-events: none;
            z-index: 21;
            text-shadow:
                0 2px 3px rgba(0, 0, 0, .18);
        }

        .id-card-expired-label {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) rotate(-12deg);
            z-index: 22;
            background: #dc2626;
            color: #fff;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            white-space: nowrap;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .22);
            pointer-events: none;
        }

        /* =========================================================
           APPLICATION STATUS
        ========================================================= */

        .application-status-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 45px 30px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .055);
        }

        .application-status-icon {
            width: 78px;
            height: 78px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .application-status-icon.incomplete {
            background: #fef2f2;
            color: #dc2626;
        }

        .application-status-icon.pending {
            background: #fffbeb;
            color: #d97706;
        }

        .application-status-icon.rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .application-status-card h3 {
            margin: 0 0 10px;
            color: #111827;
            font-size: 24px;
            font-weight: 800;
        }

        .application-status-card p {
            max-width: 650px;
            margin: 0 auto 22px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.8;
        }

        .rejection-box {
            max-width: 650px;
            margin: 0 auto 24px;
            padding: 18px 20px;
            text-align: left;
            border-radius: 12px;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .rejection-box-title {
            color: #991b1b;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .rejection-box-text {
            color: #7f1d1d;
            font-size: 13px;
            line-height: 1.7;
        }

        /* =========================================================
           MEMBERSHIP DOCUMENTS
        ========================================================= */

        .membership-document-item {
            height: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 18px;
            background: #fff;
            transition: all .2s ease;
        }

        .membership-document-item:hover {
            border-color: #d1d5db;
            box-shadow: 0 5px 18px rgba(0, 0, 0, .045);
            transform: translateY(-1px);
        }

        .membership-document-icon {
            width: 46px;
            height: 46px;
            border-radius: 11px;
            background: #ecfdf5;
            color: #047857;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .membership-document-name {
            margin: 0 0 6px;
            color: #111827;
            font-size: 15px;
            font-weight: 800;
            line-height: 1.4;
        }

        .membership-document-code {
            color: #6b7280;
            font-size: 11px;
            line-height: 1.5;
            word-break: break-word;
            margin-bottom: 12px;
        }

        .membership-document-empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            border-radius: 16px;
            background: #f3f4f6;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 768px) {

            .nacp-dashboard {
                padding: 15px 0 35px;
            }

            .nacp-dashboard .container-fluid {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            .nacp-hero {
                padding: 24px 21px;
                border-radius: 16px;
            }

            .nacp-hero h1 {
                font-size: 24px;
            }

            .nacp-hero p {
                font-size: 13px;
            }

            .nacp-hero-badge {
                font-size: 11px;
            }

            .payment-required-header {
                padding: 23px;
                flex-direction: column;
                align-items: flex-start;
            }

            .payment-required-header h3 {
                font-size: 19px;
            }

            .payment-required-header p {
                font-size: 13px;
            }

            .payment-required-body {
                padding: 23px;
            }

            .payment-step strong {
                font-size: 14px;
            }

            .payment-step span {
                font-size: 12px;
            }

            .payment-action {
                flex-direction: column;
                align-items: stretch;
            }

            .payment-action .nacp-btn {
                width: 100%;
            }

            .member-profile {
                align-items: flex-start;
            }

            .member-name {
                font-size: 18px;
            }

            .member-type {
                font-size: 12px;
            }

            .nacp-card-header {
                padding: 17px;
            }

            .nacp-card-title {
                font-size: 16px;
            }

            .nacp-card-subtitle {
                font-size: 12px;
            }

            .nacp-card-body {
                padding: 17px;
            }

            .nacp-stat {
                padding: 17px;
            }

            .nacp-stat-label {
                font-size: 12px;
            }

            .nacp-stat-value {
                font-size: 25px;
            }

            .nacp-stat-description {
                font-size: 12px;
            }

            .nacp-detail {
                gap: 10px;
            }

            .nacp-detail-label {
                font-size: 12px;
            }

            .nacp-detail-value {
                font-size: 13px;
            }

            .payment-summary h5 {
                font-size: 15px;
            }

            .payment-summary p {
                font-size: 12px;
            }

            .quick-action-title {
                font-size: 12px;
            }

            .quick-action-description {
                font-size: 11px;
            }

            .application-status-card {
                padding: 35px 20px;
            }

            .application-status-card h3 {
                font-size: 21px;
            }

            .application-status-card p {
                font-size: 13px;
            }

            .membership-document-item {
                padding: 16px;
            }

            /* =====================================================
               MOBILE EXPIRED / UNPAID ID CARD OVERLAY
            ====================================================== */

            .id-card-unpaid::after,
            .id-card-expired::after {
                width: 88%;
                height: 84%;
                border-width: 10px;
            }

            .id-card-unpaid::before,
            .id-card-expired::before {
                font-size: 170px;
            }

            .id-card-unpaid-label,
            .id-card-expired-label {
                font-size: 11px;
                padding: 7px 12px;
                letter-spacing: .7px;
            }
        }
    </style>


    <div class="nacp-dashboard">

        <div class="container-fluid px-4">

            {{-- =====================================================
             HERO
        ====================================================== --}}

            <div class="nacp-hero">

                <div class="nacp-hero-content">

                    <div class="nacp-hero-badge">
                        <i class="fas fa-leaf"></i>
                        NACPDEAN MEMBER PORTAL
                    </div>

                    <h1>
                        Member Dashboard
                    </h1>

                    <p>
                        Manage your membership, profile, payments and account information.
                    </p>

                </div>

            </div>


            {{-- =====================================================
             SUCCESS MESSAGE
        ====================================================== --}}

            @if (session('success'))
                <div class="alert alert-success nacp-alert">

                    <i class="fas fa-check-circle me-2"></i>

                    {{ session('success') }}

                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert">
                    </button>

                </div>
            @endif


            {{-- =====================================================
             STAGE 1 — PAYMENT NOT YET MADE
        ====================================================== --}}

            @if (!$hasPaid)

                <div class="alert alert-info nacp-alert">

                    <i class="fas fa-info-circle me-2"></i>

                    <strong>Welcome to NACPDEAN.</strong>

                    Please complete your membership registration payment
                    to continue with your application.

                </div>


                <div class="payment-required">

                    <div class="payment-required-header">

                        <div class="payment-required-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>

                        <div>

                            <h3>
                                Membership Payment Required
                            </h3>

                            <p>
                                Your account has been created successfully.
                                Complete your membership payment to continue
                                with your NACPDEAN application.
                            </p>

                        </div>

                    </div>


                    <div class="payment-required-body">

                        <div class="payment-step">

                            <div class="payment-step-number">
                                1
                            </div>

                            <div>

                                <strong>
                                    Complete Membership Payment
                                </strong>

                                <span>
                                    Select your membership category and make
                                    the required payment securely through Paystack.
                                </span>

                            </div>

                        </div>


                        <div class="payment-step">

                            <div class="payment-step-number">
                                2
                            </div>

                            <div>

                                <strong>
                                    Complete Your Profile
                                </strong>

                                <span>
                                    Provide the required personal and membership
                                    information after payment.
                                </span>

                            </div>

                        </div>


                        <div class="payment-step">

                            <div class="payment-step-number">
                                3
                            </div>

                            <div>

                                <strong>
                                    Submit Your Application
                                </strong>

                                <span>
                                    Submit your completed application for
                                    administrative review and approval.
                                </span>

                            </div>

                        </div>


                        <div class="payment-action">

                            <div>

                                <strong>
                                    Ready to continue?
                                </strong>

                                <span>
                                    Secure payment processing powered by Paystack.
                                </span>

                            </div>

                            <a href="{{ route('payment.index') }}" class="nacp-btn">

                                <i class="fas fa-lock"></i>

                                Make Membership Payment

                            </a>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
             PAYMENT MADE — PROFILE STATUS CONTROLS DASHBOARD
        ====================================================== --}}
            @else
                {{-- =================================================
                 NO PROFILE / DRAFT
            ================================================== --}}

                @if (!$profile || $profile->status === 'draft')

                    <div class="application-status-card">

                        <div class="application-status-icon incomplete">

                            <i class="fas fa-user-edit"></i>

                        </div>

                        <h3>
                            Profile Not Yet Completed
                        </h3>

                        <p>
                            Your membership payment has been received successfully,
                            but your membership profile has not yet been completed.
                            Please complete your profile and submit your application
                            for administrative review.
                        </p>

                        <a href="{{ route('member.profile') }}" class="nacp-btn">

                            <i class="fas fa-user-edit"></i>

                            Complete My Profile

                        </a>

                    </div>


                    {{-- =================================================
                 APPLICATION SUBMITTED
            ================================================== --}}
                @elseif ($profile->status === 'submitted')
                    <div class="application-status-card">

                        <div class="application-status-icon pending">

                            <i class="fas fa-clock"></i>

                        </div>

                        <h3>
                            Application Awaiting Approval
                        </h3>

                        <p>
                            Your membership application has been successfully
                            submitted and is currently awaiting administrative
                            review and approval.
                        </p>

                        <span class="nacp-status pending">

                            <i class="fas fa-hourglass-half"></i>

                            Awaiting Administrative Approval

                        </span>

                    </div>


                    {{-- =================================================
                 APPLICATION REJECTED
            ================================================== --}}
                @elseif ($profile->status === 'rejected')
                    <div class="application-status-card">

                        <div class="application-status-icon rejected">

                            <i class="fas fa-times-circle"></i>

                        </div>

                        <h3>
                            Application Rejected
                        </h3>

                        <p>
                            Your membership application has been reviewed by
                            the administrator and requires correction before
                            it can be approved.
                        </p>


                        @if ($profile->rejection_reason)
                            <div class="rejection-box">

                                <div class="rejection-box-title">

                                    <i class="fas fa-comment-alt me-1"></i>

                                    Administrator's Reason

                                </div>

                                <div class="rejection-box-text">

                                    {{ $profile->rejection_reason }}

                                </div>

                            </div>
                        @endif


                        @if ($profile->admin_comment)
                            <div class="rejection-box">

                                <div class="rejection-box-title">

                                    <i class="fas fa-comment-dots me-1"></i>

                                    Administrator's Comment

                                </div>

                                <div class="rejection-box-text">

                                    {{ $profile->admin_comment }}

                                </div>

                            </div>
                        @endif


                        <a href="{{ route('member.profile') }}" class="nacp-btn">

                            <i class="fas fa-edit"></i>

                            Update & Resubmit Application

                        </a>

                    </div>


                    {{-- =================================================
                 APPLICATION APPROVED
            ================================================== --}}
                @elseif ($profile->status === 'approved')
                    {{-- =================================================
                     MEMBER OVERVIEW
                ================================================== --}}

                    <div class="row g-4 mb-4">

                        {{-- =============================================
                         MEMBER PROFILE
                    ============================================== --}}

                        <div class="col-xl-6 col-lg-6">

                            <div class="nacp-card">

                                <div class="nacp-card-header">

                                    <div>

                                        <h5 class="nacp-card-title">
                                            Member Profile
                                        </h5>

                                        <p class="nacp-card-subtitle">
                                            Your registered membership information
                                        </p>

                                    </div>

                                    <div class="nacp-card-header-icon">

                                        <i class="fas fa-user"></i>

                                    </div>

                                </div>


                                <div class="nacp-card-body">

                                    <div class="member-profile">

                                        @if ($profile && $profile->photo)
                                            <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                                alt="Member Photo" class="member-photo"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                            <div class="member-photo-placeholder" style="display:none;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @else
                                            <div class="member-photo-placeholder">

                                                <i class="fas fa-user"></i>

                                            </div>
                                        @endif


                                        <div>

                                            <div class="member-name">
                                                {{ $memberName }}
                                            </div>

                                            <div class="member-type">

                                                {{ Auth::user()->member_type === 'affiliate' ? 'Affiliate Member' : 'Regular Member' }}

                                            </div>

                                        </div>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Membership Number
                                        </span>

                                        <span class="nacp-detail-value">

                                            {{ $profile->membership_number ?? 'Not assigned' }}

                                        </span>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Member Category
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipCategory)
                                                {{ $membershipCategory->name }}
                                            @else
                                                Not assigned
                                            @endif

                                        </span>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Payment Date
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipPaymentDate)
                                                {{ Carbon\Carbon::parse($membershipPaymentDate)->format('d M Y') }}
                                            @else
                                                Not available
                                            @endif

                                        </span>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Membership Expiration
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipExpirationDate)
                                                {{ $membershipExpirationDate->format('d M Y') }}
                                            @else
                                                Not available
                                            @endif

                                        </span>

                                    </div>


                                    {{-- MEMBERSHIP STATUS --}}

                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Membership Status
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipIsActive)
                                                <span class="nacp-status paid">

                                                    <i class="fas fa-check-circle"></i>

                                                    Active

                                                </span>
                                            @else
                                                <span class="nacp-status expired">

                                                    <i class="fas fa-exclamation-circle"></i>

                                                    Expired

                                                </span>
                                            @endif

                                        </span>

                                    </div>


                                    <div class="mt-3">

                                        <a href="{{ route('member.profile') }}" class="nacp-btn">

                                            <i class="fas fa-user-edit"></i>

                                            View / Update Profile

                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =============================================
                         MEMBER ID CARD
                    ============================================== --}}

                        <div class="col-xl-6 col-lg-6">

                            <div class="nacp-card">

                                <div class="nacp-card-header">

                                    <div>

                                        <h5 class="nacp-card-title">
                                            My ID Card
                                        </h5>

                                        <p class="nacp-card-subtitle">
                                            Your NACPDEAN membership identification card
                                        </p>

                                    </div>

                                    <div class="nacp-card-header-icon">

                                        <i class="fas fa-id-card"></i>

                                    </div>

                                </div>


                                <div class="nacp-card-body">

                                    <div class="id-card-preview">

                                        @if ($membershipCard)

                                            {{-- =================================================
                                             ID CARD STATUS CLASS
                                             UNPAID TAKES PRIORITY.
                                             OTHERWISE, IF MEMBERSHIP IS NOT ACTIVE,
                                             SHOW EXPIRED / INACTIVE OVERLAY.
                                        ================================================== --}}

                                            <div
                                                class="nacp-generated-id-card
                                            {{ $membershipDebitNotPaid ? 'id-card-unpaid' : (!$membershipIsActive ? 'id-card-expired' : '') }}">

                                                {{-- =================================================
                                                 EXPIRED / INACTIVE LABEL
                                                 ONLY SHOWN WHEN PAYMENT IS NOT THE ISSUE
                                            ================================================== --}}

                                                @if (!$membershipDebitNotPaid && !$membershipIsActive)
                                                    <div class="id-card-expired-label">
                                                        MEMBERSHIP EXPIRED
                                                    </div>
                                                @endif


                                                {{-- =================================================
                                                 UNPAID LABEL
                                                 EXISTING BEHAVIOUR
                                            ================================================== --}}

                                                @if ($membershipDebitNotPaid)
                                                    <div class="id-card-unpaid-label">
                                                        PAYMENT REQUIRED
                                                    </div>
                                                @endif


                                                {{-- CARD HEADER --}}

                                                <div class="nacp-id-header">

                                                    <div class="nacp-id-logo">

                                                        <i class="fas fa-leaf"></i>

                                                    </div>

                                                    <div class="nacp-id-header-text">

                                                        <div class="nacp-id-organization">
                                                            NACPDEAN
                                                        </div>

                                                        <div class="nacp-id-subtitle">
                                                            NATIONAL MEMBERSHIP ID CARD
                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- CARD BODY --}}

                                                <div class="nacp-id-body">

                                                    {{-- PHOTO --}}

                                                    <div class="nacp-id-photo">

                                                        @if ($profile && $profile->photo)
                                                            <img src="{{ asset('uploads/member_profiles/' . $profile->photo) }}"
                                                                alt="Member Photo">
                                                        @else
                                                            <div class="nacp-id-photo-placeholder">

                                                                <i class="fas fa-user"></i>

                                                            </div>
                                                        @endif

                                                    </div>


                                                    {{-- MEMBER INFORMATION --}}

                                                    <div class="nacp-id-information">

                                                        <div class="nacp-id-name">
                                                            {{ $memberName }}
                                                        </div>

                                                        <div class="nacp-id-category">

                                                            {{ $membershipCategory->name ?? 'Member' }}

                                                        </div>


                                                        <div class="nacp-id-detail">

                                                            <span>
                                                                Membership No.
                                                            </span>

                                                            <strong>
                                                                {{ $membershipCard->membership_number }}
                                                            </strong>

                                                        </div>


                                                        <div class="nacp-id-detail">

                                                            <span>
                                                                Card No.
                                                            </span>

                                                            <strong>
                                                                {{ $membershipCard->card_number }}
                                                            </strong>

                                                        </div>


                                                        <div class="nacp-id-detail">

                                                            <span>
                                                                Issued
                                                            </span>

                                                            <strong>

                                                                {{ \Carbon\Carbon::parse($membershipCard->issued_at)->format('d M Y') }}

                                                            </strong>

                                                        </div>


                                                        <div class="nacp-id-detail">

                                                            <span>
                                                                Expires
                                                            </span>

                                                            <strong>

                                                                {{ $membershipExpirationDate ? $membershipExpirationDate->format('d M Y') : 'Not available' }}

                                                            </strong>

                                                        </div>

                                                    </div>

                                                </div>


                                                {{-- CARD FOOTER --}}

                                                <div class="nacp-id-footer">

                                                    @if ($membershipIsActive)
                                                        <span>

                                                            <i class="fas fa-check-circle"></i>

                                                            ACTIVE MEMBER

                                                        </span>
                                                    @else
                                                        <span style="color:#dc2626;">

                                                            <i class="fas fa-exclamation-circle"></i>

                                                            MEMBERSHIP EXPIRED

                                                        </span>
                                                    @endif

                                                    <span>
                                                        NACPDEAN
                                                    </span>

                                                </div>

                                            </div>
                                        @else
                                            <div class="text-center py-5">

                                                <i class="fas fa-id-card" style="font-size:50px;color:#9ca3af;">
                                                </i>

                                                <h6 class="mt-3 mb-1">
                                                    ID Card Not Available
                                                </h6>

                                                <p class="text-muted small mb-0">
                                                    Your membership card has not yet been generated.
                                                </p>

                                            </div>

                                        @endif

                                    </div>


                                    <div class="id-card-actions">

                                        <div>

                                            <strong>
                                                Membership ID Card
                                            </strong>

                                            <span>
                                                Keep your membership identification card
                                                available for official use.
                                            </span>

                                        </div>


                                        {{-- ACTIVE MEMBERSHIP --}}

                                        @if ($membershipCard && !$membershipDebitNotPaid && $membershipIsActive)
                                            <a href="{{ route('membership.card') }}" class="nacp-btn-outline">

                                                <i class="fas fa-expand"></i>

                                                View Card

                                            </a>

                                            {{-- EXPIRED MEMBERSHIP --}}
                                        @elseif ($membershipCard && !$membershipDebitNotPaid && !$membershipIsActive)
                                            <span class="nacp-status expired">

                                                <i class="fas fa-exclamation-circle"></i>

                                                Membership Expired

                                            </span>
                                        @endif


                                        {{-- UNPAID MEMBERSHIP --}}

                                        @if ($membershipCard && $membershipDebitNotPaid)
                                            <a href="{{ route('membership.renewal') }}" class="nacp-btn-outline">

                                                <i class="fas fa-sync-alt"></i>

                                                Renew Membership

                                            </a>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                     MY MEMBERSHIP DOCUMENTS
                     ONLY VISIBLE WHILE MEMBERSHIP IS ACTIVE
                ================================================== --}}




                    {{-- =================================================
                     SECOND ROW
                ================================================== --}}

                    <div class="row g-4">

                        {{-- =============================================
                         MEMBERSHIP FEE
                    ============================================== --}}

                        <div class="col-xl-5 col-lg-6">

                            <div class="nacp-card">

                                <div class="nacp-card-header">

                                    <div>

                                        <h5 class="nacp-card-title">
                                            Membership Fee
                                        </h5>

                                        <p class="nacp-card-subtitle text-danger">
                                            Outstanding balance
                                        </p>

                                    </div>

                                    <div class="nacp-card-header-icon">

                                        <i class="fas fa-file-invoice-dollar"></i>

                                    </div>

                                </div>


                                <div class="nacp-card-body">

                                    <div class="membership-highlight">

                                        <div class="membership-highlight-label">
                                            Outstanding Balance
                                        </div>

                                        <div class="membership-highlight-value">

                                            ₦{{ number_format($outstandingBalance, 2) }}

                                        </div>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Paid Date
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipPaymentDate)
                                                {{ Carbon\Carbon::parse($membershipPaymentDate)->format('d M Y') }}
                                            @else
                                                Not paid
                                            @endif

                                        </span>

                                    </div>


                                    <div class="nacp-detail">

                                        <span class="nacp-detail-label">
                                            Expiration
                                        </span>

                                        <span class="nacp-detail-value">

                                            @if ($membershipExpirationDate)
                                                {{ $membershipExpirationDate->format('d M Y') }}
                                            @else
                                                Not available
                                            @endif

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =============================================
                         PAYMENTS
                    ============================================== --}}

                        <div class="col-xl-4 col-lg-6">

                            <div class="nacp-card">

                                <div class="nacp-card-header">

                                    <div>

                                        <h5 class="nacp-card-title">
                                            Payments
                                        </h5>

                                        <p class="nacp-card-subtitle">
                                            Manage additional payment obligations
                                        </p>

                                    </div>

                                    <div class="nacp-card-header-icon" style="background:#eff6ff;color:#2563eb;">

                                        <i class="fas fa-credit-card"></i>

                                    </div>

                                </div>


                                <div class="nacp-card-body">

                                    <div class="payment-summary mb-4">

                                        <div class="payment-summary-icon">

                                            <i class="fas fa-receipt"></i>

                                        </div>

                                        <div>

                                            <h5>
                                                Make a Payment
                                            </h5>

                                            <p>
                                                Pay for ID cards, certificates,
                                                penalties, renewals and other
                                                membership-related charges.
                                            </p>

                                        </div>

                                    </div>


                                    <a href="{{ route('payment.additional') }}" class="nacp-btn w-100">

                                        <i class="fas fa-credit-card"></i>

                                        View Payment Options

                                    </a>

                                </div>

                            </div>

                        </div>


                        {{-- =============================================
                         QUICK ACTIONS
                    ============================================== --}}

                        <div class="col-xl-3 col-lg-12">

                            <div class="nacp-card">

                                <div class="nacp-card-header">

                                    <div>

                                        <h5 class="nacp-card-title">
                                            Quick Actions
                                        </h5>

                                        <p class="nacp-card-subtitle">
                                            Access your account
                                        </p>

                                    </div>

                                    <div class="nacp-card-header-icon" style="background:#f5f3ff;color:#7c3aed;">

                                        <i class="fas fa-bolt"></i>

                                    </div>

                                </div>


                                <div class="nacp-card-body">

                                    <a href="{{ route('member.profile') }}" class="quick-action">

                                        <div class="quick-action-left">

                                            <div class="quick-action-icon">

                                                <i class="fas fa-user"></i>

                                            </div>

                                            <div>

                                                <span class="quick-action-title">
                                                    My Profile
                                                </span>

                                                <span class="quick-action-description">
                                                    View and update profile
                                                </span>

                                            </div>

                                        </div>

                                        <i class="fas fa-chevron-right quick-action-arrow"></i>

                                    </a>


                                    <a href="{{ route('payment.index') }}" class="quick-action">

                                        <div class="quick-action-left">

                                            <div class="quick-action-icon">

                                                <i class="fas fa-credit-card"></i>

                                            </div>

                                            <div>

                                                <span class="quick-action-title">
                                                    Payments
                                                </span>

                                                <span class="quick-action-description">
                                                    View payment obligations
                                                </span>

                                            </div>

                                        </div>

                                        <i class="fas fa-chevron-right quick-action-arrow"></i>

                                    </a>


                                    {{-- =====================================
                                     MY DOCUMENTS
                                     ONLY VISIBLE WHILE MEMBERSHIP ACTIVE
                                ====================================== --}}

                                    @if ($membershipIsActive)
                                        <a href="{{ route('member.documents.index') }}" class="quick-action">

                                            <div class="quick-action-left">

                                                <div class="quick-action-icon">

                                                    <i class="fas fa-file-alt"></i>

                                                </div>

                                                <div>

                                                    <span class="quick-action-title">
                                                        My Documents
                                                    </span>

                                                    <span class="quick-action-description">
                                                        View my membership documents
                                                    </span>

                                                </div>

                                            </div>

                                            <i class="fas fa-chevron-right quick-action-arrow"></i>

                                        </a>
                                    @endif


                                    {{-- =====================================
                                     MEMBERSHIP STATUS
                                ====================================== --}}

                                    <div class="quick-action">

                                        <div class="quick-action-left">

                                            <div class="quick-action-icon">

                                                <i class="fas fa-check-circle"></i>

                                            </div>

                                            <div>

                                                <span class="quick-action-title">
                                                    Membership
                                                </span>

                                                <span class="quick-action-description">
                                                    Status
                                                </span>

                                            </div>

                                        </div>


                                        @if ($membershipIsActive)
                                            <span class="nacp-status paid">

                                                <i class="fas fa-check-circle"></i>

                                                Active

                                            </span>
                                        @else
                                            <span class="nacp-status expired">

                                                <i class="fas fa-exclamation-circle"></i>

                                                Expired

                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            @endif

        </div>

    </div>


@endsection
