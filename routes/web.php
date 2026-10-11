<?php

use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Member\DashboardController as MemberDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExistingMemberController;
use App\Http\Controllers\Admin\MemberApplicationController;
use App\Http\Controllers\Admin\BlackListedMemberController;
use App\Http\Controllers\Admin\PenaltyController;
use App\Http\Controllers\Admin\TaskForceController;
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
use App\Http\Controllers\Admin\MembershipOfficerController;
use App\Http\Controllers\Member\MemberPenaltyController;
use App\Http\Controllers\StakeholderDirectoryController;
use App\Http\Controllers\Admin\StakeholderDirectoryController as AdminStakeholderFileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\GalleryPhotoController as AdminGalleryPhotoController;
use App\Http\Controllers\Admin\HomeVideoController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/stakeholders-data', [StakeholderDirectoryController::class, 'index'])
    ->name('stakeholders-data');

Route::get('/stakeholders-data/{stakeholderDirectory}/view', [StakeholderDirectoryController::class, 'view'])
    ->name('stakeholders-data.view');

Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('gallery');

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
Route::get('/blacklisted-members', [HomeController::class, 'BlackListed'])->name('blacklisted-members');
Route::get('/compliance', [HomeController::class, 'Compliance'])->name('compliance');
// Route::get('/gallery', [HomeController::class, 'Gallery'])->name('gallery');
Route::get('/contact', [HomeController::class, 'Contact'])->name('contact');


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
| ADMIN/SUPER ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user.role:admin,super_admin'])
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

        Route::get('/stakeholder-files', [AdminStakeholderFileController::class, 'index'])->name('stakeholder-files.index');
        Route::get('/stakeholder-files/create', [AdminStakeholderFileController::class, 'create'])->name('stakeholder-files.create');
        Route::post('/stakeholder-files', [AdminStakeholderFileController::class, 'store'])->name('stakeholder-files.store');
        Route::delete('/stakeholder-files/{stakeholderFile}', [AdminStakeholderFileController::class, 'destroy'])->name('stakeholder-files.destroy');
        Route::post('/stakeholder-files/{stakeholderFile}/toggle', [AdminStakeholderFileController::class, 'toggle'])->name('stakeholder-files.toggle');

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


        Route::get('/membership-officers/create', [MembershipOfficerController::class, 'create'])->name('membership-officers.create');
        Route::post('/membership-officers', [MembershipOfficerController::class, 'store'])->name('membership-officers.store');
        Route::get('/membership-officers/{id}', [MembershipOfficerController::class, 'show'])->name('membership-officers.show');
        Route::post('/membership-officers/{id}/approve', [MembershipOfficerController::class, 'approve'])->name('membership-officers.approve');
        Route::post('/membership-officers/{id}/reject', [MembershipOfficerController::class, 'reject'])->name('membership-officers.reject');
        Route::get('/memberships/{id}/generate-documents-manually', [MemberApplicationController::class, 'generateMembershipDocumentsManually'])->name('admin.memberships.generate-documents-manually');
        Route::get('/memberships/generate-manual-documents/{start}/{end}', [MemberApplicationController::class, 'generateManualDocumentsForRange'])->name('admin.memberships.generate-documents-range');


        Route::get('/new/members',        [AdminMemberController::class, 'index'])->name('member.index');
        Route::get('member/create', [AdminMemberController::class, 'create'])->name('member.create');
        Route::post('/all/members',       [AdminMemberController::class, 'store'])->name('member.store');
        Route::get('new/members/{member}/pay', [AdminMemberController::class, 'pay'])->name('member.pay');
        Route::get('new/members/{member}/complete-profile', [AdminMemberController::class, 'completeProfile'])
            ->name('member.complete-profile');
        // Route::get('/approved/members', [AdminMemberController::class, 'approvedMembers'])
        //     ->name('member.approved');
        Route::match(['get', 'post'], '/approved/members', [AdminMemberController::class, 'approvedMembers'])
            ->name('member.approved');
        Route::get('new/members/{member}/id-card', [AdminMemberController::class, 'viewIdCardFull'])
            ->name('member.id-card');

        Route::get('new/members/{member}/renew', [AdminMemberController::class, 'renewMembership'])
            ->name('member.renew');

        Route::get('approved/members/{member}/edit', [AdminMemberController::class, 'editProfile'])
            ->name('member.edit-profile');
        Route::put('approved/members/{member}/update', [AdminMemberController::class, 'updateProfile'])
            ->name('member.update-profile');

        Route::post('approved/members/{member}/submit-profile', [AdminMemberController::class, 'submitProfile'])
            ->name('member.submit-profile');

        Route::get('/gallery', [AdminGalleryPhotoController::class, 'index'])->name('gallery.index');
        Route::get('/gallery/create', [AdminGalleryPhotoController::class, 'create'])->name('gallery.create');
        Route::post('/gallery', [AdminGalleryPhotoController::class, 'store'])->name('gallery.store');
        Route::put('/gallery/{galleryPhoto}', [AdminGalleryPhotoController::class, 'update'])->name('gallery.update');

        Route::delete('/gallery/{galleryPhoto}', [AdminGalleryPhotoController::class, 'destroy'])->name('gallery.destroy');
        Route::post('/gallery/{galleryPhoto}/toggle', [AdminGalleryPhotoController::class, 'toggle'])->name('gallery.toggle');

        /*
|----------------------------------------------------------------
| HOME VIDEOS
|----------------------------------------------------------------
*/

        Route::get('/home-videos', [HomeVideoController::class, 'index'])->name('home-videos.index');
        Route::get('/home-videos/create', [HomeVideoController::class, 'create'])->name('home-videos.create');
        Route::post('/home-videos', [HomeVideoController::class, 'store'])->name('home-videos.store');
        Route::put('/home-videos/{homeVideo}', [HomeVideoController::class, 'update'])->name('home-videos.update');
        Route::delete('/home-videos/{homeVideo}', [HomeVideoController::class, 'destroy'])->name('home-videos.destroy');
        Route::post('/home-videos/{homeVideo}/toggle', [HomeVideoController::class, 'toggle'])->name('home-videos.toggle');


        /*
        |----------------------------------------------------------------
        | BLACKLISTED MEMBERS
        |----------------------------------------------------------------
        */
        Route::get('blacklist', [BlackListedMemberController::class, 'index'])->name('blacklist.index');
        Route::get('blacklist/create', [BlackListedMemberController::class, 'create'])->name('blacklist.create');
        Route::post('blacklist', [BlackListedMemberController::class, 'store'])->name('blacklist.store');
        Route::post('blacklist/{id}/lift', [BlackListedMemberController::class, 'lift'])->name('blacklist.lift');
        Route::delete('blacklist/{id}', [BlackListedMemberController::class, 'destroy'])->name('blacklist.destroy');


        Route::post('/approved/members/{member}/make-admin', [AdminMemberController::class, 'makeAdmin'])->name('member.make-admin');
        Route::post('/approved/members/{member}/revoke-admin', [AdminMemberController::class, 'revokeAdmin'])->name('member.revoke-admin');


        Route::get('taskforce', [TaskForceController::class, 'index'])->name('taskforce.index');
        Route::get('taskforce/create', [TaskForceController::class, 'create'])->name('taskforce.create');
        Route::post('taskforce', [TaskForceController::class, 'store'])->name('taskforce.store');
        Route::post('taskforce/{appointment}/approve', [TaskForceController::class, 'approve'])->name('taskforce.approve');
        Route::post('taskforce/{appointment}/reject', [TaskForceController::class, 'reject'])->name('taskforce.reject');
        Route::delete('taskforce/{appointment}', [TaskForceController::class, 'destroy'])->name('taskforce.destroy');


        /*
        |----------------------------------------------------------------
        | STATE EXECUTIVE POSITIONS — CATALOG
        |----------------------------------------------------------------
        */
        Route::get('/taskforce/state-positions', [TaskForceController::class, 'statePositionsIndex'])->name('taskforce.state-positions.index');
        Route::post('/taskforce/state-positions', [TaskForceController::class, 'statePositionsStore'])->name('taskforce.state-positions.store');
        Route::put('/taskforce/state-positions/{position}', [TaskForceController::class, 'statePositionsUpdate'])->name('taskforce.state-positions.update');
        Route::delete('/taskforce/state-positions/{position}', [TaskForceController::class, 'statePositionsDestroy'])->name('taskforce.state-positions.destroy');
    });


/*
|--------------------------------------------------------------------------
| SUPER ADMIN ROUTES
|--------------------------------------------------------------------------
|
| These routes are only reachable by super_admin. Regular admins
| will receive a 403, even if they guess the URL.
|
*/

Route::middleware(['auth', 'user.role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |----------------------------------------------------------------
        | PENALTIES
        |----------------------------------------------------------------
        */
        Route::prefix('penalties')->name('penalties.')->group(function () {

            // Assignments — must come BEFORE /{penalty}
            Route::get('/assignments', [PenaltyController::class, 'assignments'])->name('assignments');
            Route::get('/assign', [PenaltyController::class, 'assignForm'])->name('assign.form');
            Route::post('/assign', [PenaltyController::class, 'assignStore'])->name('assign.store');
            Route::post('/assignments/{memberPenalty}/mark-paid', [PenaltyController::class, 'markPaid'])->name('mark-paid');

            // Catalog CRUD
            Route::get('/', [PenaltyController::class, 'index'])->name('index');
            Route::get('/create', [PenaltyController::class, 'create'])->name('create');
            Route::post('/', [PenaltyController::class, 'store'])->name('store');
            Route::get('/{penalty}/edit', [PenaltyController::class, 'edit'])->name('edit');
            Route::put('/{penalty}', [PenaltyController::class, 'update'])->name('update');
            Route::delete('/{penalty}', [PenaltyController::class, 'destroy'])->name('destroy');
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
        // Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        // Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize');
        Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
        Route::post('/payment/verify-existing-member', [ExistingMemberController::class, 'verify'])
            ->name('payment.verify-existing-member');

        /*
        |----------------------------------------------------------------
        | PROFILE
        |----------------------------------------------------------------
        */
        // Route::get('/profile', [MemberProfileController::class, 'index'])->name('member.profile');
        // Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
        // Route::post('/profile/submit', [MemberProfileController::class, 'submit'])->name('member.profile.submit');
        Route::get('/application-status', [MemberProfileController::class, 'applicationStatus'])
            ->name('member.application.status');

        /*
        |----------------------------------------------------------------
        | MEMBERSHIP CARD
        |----------------------------------------------------------------
        */
        Route::get('/membership-card', [MembershipCardController::class, 'index'])->name('membership.card');
        Route::get('/membership-card/user/{id}', [MembershipCardController::class, 'index'])->name('membership.card.user');

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
        // Route::get('/membership/renewal', [PaymentController::class, 'membershipRenewal'])
        //     ->name('membership.renewal');
        // Route::post('/membership/renewal/initialize', [PaymentController::class, 'initializeMembershipRenewal'])
        //     ->name('membership.renewal.initialize');
        // Route::get('/membership/renewal/callback', [PaymentController::class, 'membershipRenewalCallback'])
        //     ->name('membership.renewal.callback');


        /*
|----------------------------------------------------------------
| MEMBER PENALTIES
|----------------------------------------------------------------
*/
        Route::prefix('member/penalties')->name('member.penalties.')->group(function () {

            Route::get('/', [MemberPenaltyController::class, 'index'])
                ->name('index');

            Route::post('/pay', [MemberPenaltyController::class, 'pay'])
                ->name('pay');

            Route::get('/callback', [MemberPenaltyController::class, 'callback'])
                ->name('callback');

            Route::get('/{memberPenalty}/receipt', [MemberPenaltyController::class, 'receipt'])
                ->name('receipt');
        });
    });



Route::middleware('auth')
    ->group(function () {

        Route::get('/profile', [MemberProfileController::class, 'index'])->name('member.profile');
        Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
        Route::post('/profile/submit', [MemberProfileController::class, 'submit'])->name('member.profile.submit');

        Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize');

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
