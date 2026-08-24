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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'Index'])->name('index');
Route::get('/about', [HomeController::class, 'About'])->name('about');
Route::get('/board_of_trustees', [HomeController::class, 'Bot'])->name('bot');
Route::get('/history', [HomeController::class, 'History'])->name('history');
Route::get('/association', [HomeController::class, 'Association'])->name('association');
Route::get('/partnership', [HomeController::class, 'Partnership'])->name('partnership');
Route::get('/national-executive', [HomeController::class, 'NationalExecutive'])->name('national-executive');
Route::get('/state-executive', [HomeController::class, 'StateExecutive'])->name('state-executive');
Route::get('/verify/membership/{qrToken}', [MembershipVerificationController::class, 'verify'])->name('membership.verify');


Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/verify-otp', [EmailOtpController::class, 'show'])->name('verification.otp');
Route::post('/verify-otp', [EmailOtpController::class, 'verify'])->name('verification.otp.verify');
Route::post('/resend-otp', [EmailOtpController::class, 'resend'])->name('verification.otp.resend');



Route::middleware(['auth', 'user.role:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])
        ->name('admin.admin_dashboard');
    Route::get('/admin/logout', [AdminDashboard::class, 'AdminLogout'])->name('admin.logout');

    Route::get('/admin/members', [MemberApplicationController::class, 'index'])->name('admin.members.index');
    Route::get('/admin/members/{id}', [MemberApplicationController::class, 'show'])->name('admin.members.show');
    Route::post('/admin/members/{id}/approve', [MemberApplicationController::class, 'approve'])->name('admin.members.approve');
    Route::post('/admin/members/{id}/reject', [MemberApplicationController::class, 'reject'])->name('admin.members.reject');
});


Route::middleware(['auth', 'user.role:member'])->group(function () {

    Route::get('/member/dashboard', [MemberDashboard::class, 'index'])
        ->name('member.member_dashboard');
    Route::get('/member/logout', [MemberDashboard::class, 'MemberLogout'])->name('member.logout');

    Route::get('/payment', [PaymentController::class, 'index'])
        ->name('payment.index');

    Route::post('/payment/initialize', [PaymentController::class, 'initialize'])
        ->name('payment.initialize');

    Route::get('/payment/success', [PaymentController::class, 'success'])
        ->name('payment.success');
    Route::post('/payment/verify-existing-member', [ExistingMemberController::class, 'verify'])->name('payment.verify-existing-member');


    Route::get('/profile', [MemberProfileController::class, 'index'])
        ->name('member.profile');

    Route::put('/profile', [MemberProfileController::class, 'update'])
        ->name('member.profile.update');

    Route::post('/profile/submit', [MemberProfileController::class, 'submit'])
        ->name('member.profile.submit');

    Route::get('/application-status', [MemberProfileController::class, 'applicationStatus'])
        ->name('member.application.status');

    Route::get('/membership-card', [MembershipCardController::class, 'index'])->name('membership.card');

    Route::get('/payment/additional', [PaymentController::class, 'additionalPayments'])
        ->name('payment.additional');

    Route::post('/payment/additional/initialize', [PaymentController::class, 'initializeAdditionalPayment'])
        ->name('payment.additional.initialize');

    Route::get('/payment/additional/callback', [PaymentController::class, 'additionalPaymentCallback'])
        ->name('payment.additional.callback');
});



Route::middleware(['auth', 'user.role:user'])->group(function () {

    Route::get('/user/dashboard', [UserDashboard::class, 'index'])
        ->name('user.user_dashboard');
});




// Route::middleware('auth')->group(function () {
//     Route::get('/payment', [PaymentController::class, 'index'])
//         ->name('payment.index');

//     Route::post('/payment/initialize', [PaymentController::class, 'initialize'])
//         ->name('payment.initialize');

//     Route::get('/payment/success', [PaymentController::class, 'success'])
//         ->name('payment.success');
//     Route::post('/payment/verify-existing-member', [ExistingMemberController::class, 'verify'])->name('payment.verify-existing-member');
// });

Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');



require __DIR__ . '/auth.php';
