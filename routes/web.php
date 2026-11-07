<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TicketTierController as AdminTicketTierController;
use App\Http\Controllers\Admin\AddonController as AdminAddonController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\RegistrantController as AdminRegistrantController;
use App\Http\Controllers\Admin\MasterDataController as AdminMasterDataController;
use App\Http\Controllers\Admin\PanitiaController as AdminPanitiaController;
use App\Http\Controllers\Admin\MidtransSyncController as AdminMidtransSyncController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\Public\ParticipantController;
use App\Http\Controllers\Public\LocaleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('home');

// Webhook Midtrans (di luar auth; signature diverifikasi di controller).
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/events', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::prefix('event')->group(function () {
    Route::get('/{slug}', [PublicEventController::class, 'show'])->name('public.events.show');
    Route::get('/{slug}/stage', [PublicEventController::class, 'stage'])->name('public.events.stage');
    Route::get('/{slug}/confirm', [PublicEventController::class, 'confirm'])->name('public.events.confirm');
    Route::post('/{slug}/store', [PublicEventController::class, 'store'])->name('public.events.store');
    Route::get('/{slug}/confirmed/{token}', [PublicEventController::class, 'confirmed'])->name('public.events.confirmed');
});

Route::prefix('participant')->group(function () {
    Route::get('/{token}/payment', [ParticipantController::class, 'payment'])->name('public.participants.payment');
    Route::post('/{token}/payment', [ParticipantController::class, 'paymentStore'])->name('public.participants.payment.store');
    Route::get('/{token}/done', [ParticipantController::class, 'done'])->name('public.participants.done');
    Route::post('/{token}/checkout', [ParticipantController::class, 'checkout'])->name('public.participants.checkout');
    Route::post('/{token}/midtrans/sync', [ParticipantController::class, 'syncMidtrans'])->name('public.participants.midtrans.sync');
    Route::get('/{token}/ticket', [ParticipantController::class, 'ticket'])->name('public.participants.ticket');
    Route::get('/{token}/ticket/print', [ParticipantController::class, 'ticketPrint'])->name('public.participants.ticket.print');
    Route::get('/{token}/ticket/download', [ParticipantController::class, 'ticketDownload'])->name('public.participants.ticket.download');
    Route::get('/{token}/checkout-letter', [ParticipantController::class, 'checkoutLetter'])->name('public.participants.checkout-letter');
    Route::get('/{token}/checkout-letter/download', [ParticipantController::class, 'checkoutLetterDownload'])->name('public.participants.checkout-letter.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
    Route::get('tutorial', function () {
        return view('admin.tutorial');
    })->name('admin.tutorial');

    Route::post('categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');

    Route::get('events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::post('events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::get('events/create', [AdminEventController::class, 'create'])->name('admin.events.create');
    Route::get('events/board', [AdminEventController::class, 'board'])->name('admin.events.board');
    Route::post('events/{event}/board', [AdminEventController::class, 'moveBoard'])->name('admin.events.board.move');
    Route::get('events/{event}/result', [AdminEventController::class, 'result'])->name('admin.events.result');
    Route::post('events/{event}/result', [AdminEventController::class, 'resultStore'])->name('admin.events.result.store');
    Route::get('events/{event}/midtrans', [AdminMidtransSyncController::class, 'index'])->name('admin.events.midtrans.index');
    Route::post('events/{event}/midtrans/test', [AdminMidtransSyncController::class, 'test'])->name('admin.events.midtrans.test');
    Route::put('events/{event}', [AdminEventController::class, 'update'])->name('admin.events.update');
    Route::delete('events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');
    Route::get('events/{event}', [AdminEventController::class, 'show'])->name('admin.events.show');
    Route::get('events/{event}/edit', [AdminEventController::class, 'edit'])->name('admin.events.edit');

    Route::prefix('events/{event}/tiers')->group(function () {
        Route::get('/', [AdminTicketTierController::class, 'index'])->name('admin.events.tiers.index');
        Route::get('/create', [AdminTicketTierController::class, 'create'])->name('admin.events.tiers.create');
        Route::post('/', [AdminTicketTierController::class, 'store'])->name('admin.events.tiers.store');
        Route::get('/{tier}/edit', [AdminTicketTierController::class, 'edit'])->name('admin.events.tiers.edit');
        Route::put('/{tier}', [AdminTicketTierController::class, 'update'])->name('admin.events.tiers.update');
        Route::delete('/{tier}', [AdminTicketTierController::class, 'destroy'])->name('admin.events.tiers.destroy');
    });

    Route::prefix('events/{event}/addons')->group(function () {
        Route::get('/', [AdminAddonController::class, 'index'])->name('admin.events.addons.index');
        Route::get('/create', [AdminAddonController::class, 'create'])->name('admin.events.addons.create');
        Route::post('/', [AdminAddonController::class, 'store'])->name('admin.events.addons.store');
        Route::get('/{addon}/edit', [AdminAddonController::class, 'edit'])->name('admin.events.addons.edit');
        Route::put('/{addon}', [AdminAddonController::class, 'update'])->name('admin.events.addons.update');
        Route::delete('/{addon}', [AdminAddonController::class, 'destroy'])->name('admin.events.addons.destroy');
    });

    Route::prefix('events/{event}/vouchers')->group(function () {
        Route::get('/', [AdminVoucherController::class, 'index'])->name('admin.events.vouchers.index');
        Route::get('/create', [AdminVoucherController::class, 'create'])->name('admin.events.vouchers.create');
        Route::post('/', [AdminVoucherController::class, 'store'])->name('admin.events.vouchers.store');
        Route::get('/{voucher}/edit', [AdminVoucherController::class, 'edit'])->name('admin.events.vouchers.edit');
        Route::put('/{voucher}', [AdminVoucherController::class, 'update'])->name('admin.events.vouchers.update');
        Route::delete('/{voucher}', [AdminVoucherController::class, 'destroy'])->name('admin.events.vouchers.destroy');
    });

    Route::prefix('events/{event}/registrants')->group(function () {
        Route::get('/', [AdminRegistrantController::class, 'index'])->name('admin.events.registrants.index');
        Route::get('/{registrant}', [AdminRegistrantController::class, 'show'])->name('admin.events.registrants.show');
        Route::put('/{registrant}', [AdminRegistrantController::class, 'update'])->name('admin.events.registrants.update');
    });

    Route::prefix('events/{event}/panitia')->group(function () {
        Route::get('/', [AdminPanitiaController::class, 'index'])->name('admin.events.panitia.index');
        Route::post('/', [AdminPanitiaController::class, 'store'])->name('admin.events.panitia.store');
        Route::put('/{member}/role', [AdminPanitiaController::class, 'updateRole'])->name('admin.events.panitia.role');
        Route::delete('/{member}', [AdminPanitiaController::class, 'destroy'])->name('admin.events.panitia.destroy');
        Route::post('/{member}/payroll', [AdminPanitiaController::class, 'payrollStore'])->name('admin.events.panitia.payroll.store');
        Route::post('/{member}/payroll/{payroll}/pay', [AdminPanitiaController::class, 'payrollPay'])->name('admin.events.panitia.payroll.pay');
        Route::get('/{member}/id-card', [AdminPanitiaController::class, 'idCard'])->name('admin.events.panitia.id-card');
    });

    // Impor/ekspor master data per event: tiers, addons, vouchers, registrants.
    Route::get('events/{event}/export/{type}', [AdminMasterDataController::class, 'export'])
        ->name('admin.events.export');
    Route::get('events/{event}/export/{type}/template', [AdminMasterDataController::class, 'template'])
        ->name('admin.events.export.template');
    Route::post('events/{event}/import/{type}', [AdminMasterDataController::class, 'import'])
        ->name('admin.events.import');
});
