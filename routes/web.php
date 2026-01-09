<?php

use App\Http\Controllers\AppDashboardController;
use App\Http\Controllers\AppDocumentsController;
use App\Http\Controllers\AppDocumentVersionsController;
use App\Http\Controllers\AppMilestonesController;
use App\Http\Controllers\AppOffersController;
use App\Http\Controllers\AppOrdersController;
use App\Http\Controllers\AppOrganizationsController;
use App\Http\Controllers\AppReviewRequestsController;
use App\Http\Controllers\AppTasksController;
use App\Http\Controllers\AppTicketsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\PortalDashboardController;
use App\Http\Controllers\PortalEstimatesController;
use App\Http\Controllers\PortalMilestonesController;
use App\Http\Controllers\PortalOffersController;
use App\Http\Controllers\PortalReviewsController;
use App\Http\Controllers\PortalTicketsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});
Route::get('/index.php', function () {
    return redirect('/login');
});
Route::get('/index.php/{any}', function (string $any) {
    return redirect('/' . $any);
})->where('any', '.*');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/invite/{token}', [AuthController::class, 'showInvite']);
Route::post('/invite/{token}/accept', [AuthController::class, 'acceptInvite']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth')->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', [AppDashboardController::class, 'index']);
    Route::get('/organizations', [AppOrganizationsController::class, 'index']);
    Route::get('/organizations/{id}', [AppOrganizationsController::class, 'show']);
    Route::get('/tickets', [AppTicketsController::class, 'index']);
    Route::get('/tickets/{id}', [AppTicketsController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/public-message', [AppTicketsController::class, 'addPublicMessage']);
    Route::post('/tickets/{id}/internal-note', [AppTicketsController::class, 'addInternalNote']);
    Route::get('/tickets/{id}/estimate/create', [AppTicketsController::class, 'showEstimateCreate']);
    Route::post('/tickets/{id}/estimate/send', [AppTicketsController::class, 'sendEstimate']);
    Route::post('/tickets/{id}/task-from-ticket', [AppTicketsController::class, 'taskFromTicket']);
    Route::get('/offers', [AppOffersController::class, 'index']);
    Route::get('/offers/create', [AppOffersController::class, 'create']);
    Route::post('/offers', [AppOffersController::class, 'store']);
    Route::get('/offers/{id}/edit', [AppOffersController::class, 'edit'])->name('offers.edit');
    Route::post('/offers/{id}/positions', [AppOffersController::class, 'addPosition']);
    Route::post('/offers/{id}/send', [AppOffersController::class, 'send']);
    Route::get('/orders', [AppOrdersController::class, 'index']);
    Route::get('/orders/{id}', [AppOrdersController::class, 'show']);
    Route::post('/tasks/{id}/worklogs', [AppTasksController::class, 'addWorklog']);
    Route::get('/milestones', [AppMilestonesController::class, 'index']);
    Route::get('/milestones/create', [AppMilestonesController::class, 'create']);
    Route::post('/milestones', [AppMilestonesController::class, 'store']);
    Route::get('/milestones/{id}', [AppMilestonesController::class, 'show'])->name('milestones.show');
    Route::post('/milestones/{id}/items', [AppMilestonesController::class, 'addItem']);
    Route::post('/milestones/{id}/complete', [AppMilestonesController::class, 'complete']);
    Route::post('/contexts/{type}/{id}/documents', [AppDocumentsController::class, 'store']);
    Route::post('/documents/{id}/versions', [AppDocumentVersionsController::class, 'store']);
    Route::post('/document-versions/{id}/review-request', [AppReviewRequestsController::class, 'store']);
});

Route::middleware('auth')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalDashboardController::class, 'index']);
    Route::get('/tickets', [PortalTicketsController::class, 'index']);
    Route::get('/tickets/create', [PortalTicketsController::class, 'create']);
    Route::post('/tickets', [PortalTicketsController::class, 'store']);
    Route::get('/tickets/{id}', [PortalTicketsController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [PortalTicketsController::class, 'reply']);
    Route::get('/offers', [PortalOffersController::class, 'index']);
    Route::get('/offers/{id}', [PortalOffersController::class, 'show']);
    Route::post('/offer-positions/{id}/decide', [PortalOffersController::class, 'decide']);
    Route::get('/estimates', [PortalEstimatesController::class, 'index']);
    Route::post('/estimates/{id}/decide', [PortalEstimatesController::class, 'decide']);
    Route::get('/milestones', [PortalMilestonesController::class, 'index']);
    Route::get('/milestones/{id}', [PortalMilestonesController::class, 'show']);
    Route::get('/reviews/{review_request_id}', [PortalReviewsController::class, 'show']);
    Route::post('/reviews/{review_request_id}/comments', [PortalReviewsController::class, 'comment']);
    Route::post('/reviews/{review_request_id}/decide', [PortalReviewsController::class, 'decide']);
});

Route::middleware('auth')->get('/files/document-versions/{id}', [FilesController::class, 'show']);

Route::fallback(function () {
    return redirect('/login');
});
