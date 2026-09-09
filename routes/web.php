<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Member\DashboardController as MemberDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExistingMemberController;
use App\Http\Controllers\Admin\MemberApplicationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Http\Controllers\Member\MemberProfileController;
use App\Http\Controllers\Member\MembershipCardController;
use App\Http\Controllers\Member\MembershipVerificationController;
use App\Http\Controllers\OperationalRightsDocumentController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\PaymentItemController;
use App\Http\Controllers\GeneratedDocumentController;
use App\Http\Controllers\PublicMemberController;
use App\Http\Controllers\Admin\DocumentFieldController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/exporters', [PublicMemberController::class, 'exporters'])->name('exporters');
Route::get('/producers', [PublicMemberController::class, 'producers'])->name('producers');
Route::get('/dealers', [PublicMemberController::class, 'dealers'])->name('dealers');
Route::get('/suppliers', [PublicMemberController::class, 'suppliers'])->name('suppliers');

Route::get('/', [HomeController::class, 'Index'])->name('index');
Route::get('/about', [HomeController::class, 'About'])->name('about');
Route::get('/board_of_trustees', [HomeController::class, 'Bot'])->name('bot');
Route::get('/history', [HomeController::class, 'History'])->name('history');
Route::get('/association', [HomeController::class, 'Association'])->name('association');
Route::get('/partnership', [HomeController::class, 'Partnership'])->name('partnership');
Route::get('/national-executive', [HomeController::class, 'NationalExecutive'])->name('national-executive');
Route::get('/state-executive', [HomeController::class, 'StateExecutive'])->name('state-executive');


/*
|--------------------------------------------------------------------------
| PUBLIC VERIFICATION
|--------------------------------------------------------------------------
*/

// Membership QR verification
Route::get('/verify/membership/{qrToken}', [MembershipVerificationController::class, 'verify'])
    ->name('membership.verify');

// Operational Rights QR verification
Route::get('/verify/operational-rights/{token}', [OperationalRightsDocumentController::class, 'verify'])
    ->name('documents.operational-rights.verify');

/*
| Generated Document QR verification.
|
| This route is PUBLIC because the QR code on a document must be
| scannable without requiring the person scanning it to log in.
| The controller must verify the document using tracking_code.
*/
Route::get('/verify/{trackingCode}', [GeneratedDocumentController::class, 'verify'])
    ->name('documents.verify');


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/verify-otp', [EmailOtpController::class, 'show'])->name('verification.otp');
Route::post('/verify-otp', [EmailOtpController::class, 'verify'])->name('verification.otp.verify');
Route::post('/resend-otp', [EmailOtpController::class, 'resend'])->name('verification.otp.resend');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user.role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin_dashboard');
        Route::get('/logout', [AdminDashboard::class, 'AdminLogout'])->name('logout');

        /*
        |----------------------------------------------------------------
        | MEMBER APPLICATIONS
        |----------------------------------------------------------------
        */
        Route::get('/members', [MemberApplicationController::class, 'index'])->name('members.index');
        Route::get('/members/{id}', [MemberApplicationController::class, 'show'])->name('members.show');
        Route::get('/members/{id}/document/{document}', [MemberApplicationController::class, 'viewDocument'])
            ->name('members.document');
        Route::post('/members/{id}/approve', [MemberApplicationController::class, 'approve'])->name('members.approve');
        Route::post('/members/{id}/reject', [MemberApplicationController::class, 'reject'])->name('members.reject');
        Route::post('/members/{id}/generate-renewal-debit', [MemberApplicationController::class, 'generateRenewalDebit'])
            ->name('members.generate-renewal-debit');

        /*
        |----------------------------------------------------------------
        | ADMIN DOCUMENT MANAGEMENT
        |----------------------------------------------------------------
        */
        Route::get('documents/{document}/preview', [AdminDocumentController::class, 'preview'])
            ->name('documents.preview');
        Route::resource('documents', AdminDocumentController::class);

        /*
        |----------------------------------------------------------------
        | ADMIN PAYMENT ITEMS
        |----------------------------------------------------------------
        */
        Route::resource('payment-items', PaymentItemController::class)
            ->except(['show', 'destroy']);

        /*
        |----------------------------------------------------------------
        | DOCUMENT FIELD MANAGEMENT
        |----------------------------------------------------------------
        */
        Route::prefix('documents/{document}/fields')
            ->name('documents.fields.')
            ->group(function () {
                Route::get('/', [DocumentFieldController::class, 'index'])->name('index');
                Route::get('/create', [DocumentFieldController::class, 'create'])->name('create');
                Route::post('/', [DocumentFieldController::class, 'store'])->name('store');
                Route::get('/{field}/edit', [DocumentFieldController::class, 'edit'])->name('edit');
                Route::put('/{field}', [DocumentFieldController::class, 'update'])->name('update');
                Route::delete('/{field}', [DocumentFieldController::class, 'destroy'])->name('destroy');
                Route::post('/{field}/move-up', [DocumentFieldController::class, 'moveUp'])->name('move-up');
                Route::post('/{field}/move-down', [DocumentFieldController::class, 'moveDown'])->name('move-down');
            });
    });


/*
|--------------------------------------------------------------------------
| MEMBER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user.role:member'])
    ->group(function () {

        /*
        |----------------------------------------------------------------
        | MEMBER DASHBOARD
        |----------------------------------------------------------------
        */
        Route::get('/member/dashboard', [MemberDashboard::class, 'index'])->name('member.member_dashboard');
        Route::get('/member/logout', [MemberDashboard::class, 'MemberLogout'])->name('member.logout');

        /*
        |----------------------------------------------------------------
        | PAYMENT
        |----------------------------------------------------------------
        */
        Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize');
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::post('/payment/verify-existing-member', [ExistingMemberController::class, 'verify'])
            ->name('payment.verify-existing-member');

        /*
        |----------------------------------------------------------------
        | PROFILE
        |----------------------------------------------------------------
        */
        Route::get('/profile', [MemberProfileController::class, 'index'])->name('member.profile');
        Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
        Route::post('/profile/submit', [MemberProfileController::class, 'submit'])->name('member.profile.submit');
        Route::get('/application-status', [MemberProfileController::class, 'applicationStatus'])
            ->name('member.application.status');

        /*
        |----------------------------------------------------------------
        | MEMBERSHIP CARD
        |----------------------------------------------------------------
        */
        Route::get('/membership-card', [MembershipCardController::class, 'index'])->name('membership.card');

        /*
        |----------------------------------------------------------------
        | ADDITIONAL PAYMENTS
        |----------------------------------------------------------------
        */
        Route::get('/payment/additional/fields', [PaymentController::class, 'getAdditionalPaymentFields'])
            ->name('payment.additional.fields');
        Route::get('/additional-payment', [PaymentController::class, 'additional'])->name('payment.additional');
        Route::post('/additional-payment/initialize', [PaymentController::class, 'initializeAdditional'])
            ->name('payment.additional.initialize');
        Route::get('/additional-payment/callback', [PaymentController::class, 'additionalCallback'])
            ->name('payment.additional.callback');

        /*
        |----------------------------------------------------------------
        | MEMBER OPERATIONAL RIGHTS DOCUMENTS
        |----------------------------------------------------------------
        */
        Route::get('/member/documents/operational-rights', [OperationalRightsDocumentController::class, 'index'])
            ->name('member.documents.operational-rights.index');
        Route::get('/member/documents/operational-rights/{document}', [OperationalRightsDocumentController::class, 'show'])
            ->name('member.documents.operational-rights.show');
        Route::get('/member/documents/operational-rights/{document}/pdf', [OperationalRightsDocumentController::class, 'pdf'])
            ->name('member.documents.operational-rights.pdf');
        Route::get('/member/documents/operational-rights/{document}/download', [OperationalRightsDocumentController::class, 'download'])
            ->name('member.documents.operational-rights.download');
        Route::get('/member/documents/operational-rights/{document}/print', [OperationalRightsDocumentController::class, 'print'])
            ->name('member.documents.operational-rights.print');

        /*
        |----------------------------------------------------------------
        | DOCUMENT TEMPLATES / MANUAL GENERATION
        |----------------------------------------------------------------
        */
        Route::get('/documents/{documentId}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/generated/{generatedDocument}', [DocumentController::class, 'viewDocument'])
            ->name('documents.view');
        Route::get('/documents/{documentId}/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents/{documentId}/generate', [DocumentController::class, 'store'])->name('documents.store');

        /*
        |----------------------------------------------------------------
        | GENERATED MEMBERSHIP / PAYMENT DOCUMENTS
        |----------------------------------------------------------------
        |
        | IMPORTANT:
        | These routes are protected by:
        |   1. auth middleware
        |   2. user.role:member middleware
        |   3. GeneratedDocumentController ownership checks
        |
        | The controller MUST query:
        |   where('id', $generatedDocument)->where('user_id', Auth::id())
        | before returning the document.
        */
        Route::get('/member/documents', [GeneratedDocumentController::class, 'index'])
            ->name('member.documents.index');

        Route::get('/member/documents/{generatedDocument}', [GeneratedDocumentController::class, 'show'])
            ->whereNumber('generatedDocument')
            ->name('member.documents.show');

        Route::get('/member/documents/{generatedDocument}/print', [GeneratedDocumentController::class, 'print'])
            ->whereNumber('generatedDocument')
            ->name('member.documents.print');

        Route::get('/member/documents/{generatedDocument}/download', [GeneratedDocumentController::class, 'download'])
            ->whereNumber('generatedDocument')
            ->name('member.documents.download');

        Route::get('/member/documents/{generatedDocument}/renew', [GeneratedDocumentController::class, 'renew'])
            ->whereNumber('generatedDocument')
            ->name('member.documents.renew');

        /*
        |----------------------------------------------------------------
        | MEMBERSHIP RENEWAL
        |----------------------------------------------------------------
        */
        Route::get('/membership/renewal', [PaymentController::class, 'membershipRenewal'])
            ->name('membership.renewal');
        Route::post('/membership/renewal/initialize', [PaymentController::class, 'initializeMembershipRenewal'])
            ->name('membership.renewal.initialize');
        Route::get('/membership/renewal/callback', [PaymentController::class, 'membershipRenewalCallback'])
            ->name('membership.renewal.callback');
    });


/*
|--------------------------------------------------------------------------
| REGULAR USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user.role:user'])
    ->group(function () {
        Route::get('/user/dashboard', [UserDashboard::class, 'index'])->name('user.user_dashboard');
    });


/*
|--------------------------------------------------------------------------
| PAYSTACK CALLBACK
|--------------------------------------------------------------------------
*/

Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

require __DIR__ . '/auth.php';
