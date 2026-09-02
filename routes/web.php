<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ResidentApprovalController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminIncidentController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PaymentDashboardController; 
use App\Http\Controllers\Resident\DocumentRequestController;
use App\Http\Controllers\Resident\IncidentReportController;
use App\Http\Controllers\Resident\FacilityBookingController; 
use App\Http\Controllers\Resident\AnnouncementController as ResidentAnnouncementController;
use App\Http\Controllers\Resident\HotlineController as ResidentHotlineController;
use App\Http\Controllers\Admin\HotlineController as AdminHotlineController;
use App\Http\Controllers\Resident\ResidentDigitalIdController;
use App\Models\DocumentRequest;
use App\Models\Payment;
use App\Models\FacilityPayment;
use App\Models\FacilityBooking;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/verify/document/{tracking_number}', [AdminDocumentController::class, 'verifyQrCode'])
    ->name('public.verify-document');

Route::get('/verify/payment/{reference_number}', function ($reference_number) {
    $payment = Payment::with(['resident', 'payable'])->where('reference_number', $reference_number)->first()
        ?? FacilityPayment::with(['resident', 'facilityBooking'])->where('reference_number', $reference_number)->firstOrFail();
    
    return view('payments.verify', compact('payment'));
})->name('payments.verify');


/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Switcher
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->canAccessAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('resident.dashboard');
})->middleware(['auth'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Resident Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:resident'])->prefix('resident')->name('resident.')->group(function () {
    
    Route::get('/dashboard', function () {
        $pendingCount = DocumentRequest::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        return view('resident.dashboard', compact('pendingCount'));
    })->name('dashboard');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    
    Route::post('/notifications/read-all-ajax', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.readAllAjax');

    Route::get('/hotlines', [ResidentHotlineController::class, 'index'])->name('hotlines.directory');

    // Digital Barangay ID Route
    Route::get('/digital-id', [ResidentDigitalIdController::class, 'show'])->name('digital-id');

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [DocumentRequestController::class, 'index'])->name('index');
        Route::get('/create', [DocumentRequestController::class, 'create'])->name('create');
        Route::post('/', [DocumentRequestController::class, 'store'])->name('store');
        Route::get('/{documentRequest}', [DocumentRequestController::class, 'show'])->name('show');
        Route::get('/{documentRequest}/claim-stub', [DocumentRequestController::class, 'downloadClaimStub'])->name('claim-stub');
        Route::get('/{documentRequest}/download', [DocumentRequestController::class, 'download'])->name('download');
    });

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentReportController::class, 'index'])->name('index');
        Route::get('/create', [IncidentReportController::class, 'create'])->name('create');
        Route::post('/', [IncidentReportController::class, 'store'])->name('store');
        Route::get('/{incident}', [IncidentReportController::class, 'show'])->name('show');
    });

    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [FacilityBookingController::class, 'index'])->name('index');
        Route::get('/create', [FacilityBookingController::class, 'create'])->name('create');
        Route::post('/', [FacilityBookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [FacilityBookingController::class, 'show'])->name('show');
    });

    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [ResidentAnnouncementController::class, 'index'])->name('index');
        Route::get('/{announcement}', [ResidentAnnouncementController::class, 'show'])->name('show');
    });

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Resident\PaymentController::class, 'index'])->name('index');

        Route::get('/checkout/{type}/{id}', function($type, $id) {
            if ($type === 'document') {
                $payableModel = DocumentRequest::findOrFail($id);
                $paymentRecord = Payment::where('payable_type', DocumentRequest::class)->where('payable_id', $id)->first();
            } else {
                $payableModel = FacilityBooking::findOrFail($id);
                $paymentRecord = FacilityPayment::where('facility_booking_id', $id)->first();
            }
            
            return view('resident.payments.checkout', compact('payableModel', 'type', 'paymentRecord'));
        })->name('checkout');

        Route::post('/process/{type}/{id}', [\App\Http\Controllers\Resident\PaymentController::class, 'processPayment'])->name('process');
        Route::get('/{payment}/receipt', [\App\Http\Controllers\Resident\PaymentController::class, 'showReceipt'])->name('receipt');
        Route::get('/{payment}/pdf', [\App\Http\Controllers\Resident\PaymentController::class, 'downloadPdf'])->name('pdf');
    });

});


/*
|--------------------------------------------------------------------------
| Admin, Staff, Kagawad & Captain Command Center
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/notifications', function () {
        $admin = auth()->user();
        return view('admin.notifications.index', compact('admin'));
    })->name('notifications.index');

    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    
    Route::post('/notifications/read-all-ajax', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.readAllAjax');

    Route::get('/signature', [AdminDocumentController::class, 'signatureForm'])->name('signature.form');
    Route::post('/signature', [AdminDocumentController::class, 'storeSignature'])->name('signature.store');

    Route::prefix('residents')->name('residents.')->group(function () {
        Route::get('/', [ResidentApprovalController::class, 'index'])->name('index');
        Route::get('/{resident}', [ResidentApprovalController::class, 'show'])->name('show')->whereNumber('resident');
        Route::match(['post', 'patch'], '/{resident}/approve', [ResidentApprovalController::class, 'approve'])->name('approve')->whereNumber('resident');
        Route::match(['post', 'patch'], '/{resident}/reject', [ResidentApprovalController::class, 'reject'])->name('reject')->whereNumber('resident');
    });

    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index'])->name('index');
        Route::get('/{documentRequest}', [AdminDocumentController::class, 'show'])->name('show')->whereNumber('documentRequest');
        
        Route::match(['get', 'post'], '/{documentRequest}/upload', [AdminDocumentController::class, 'uploadCompletedDocument'])->name('upload')->whereNumber('documentRequest');
        Route::match(['get', 'post'], '/{documentRequest}/upload-sign', [AdminDocumentController::class, 'adminUploadAndSign'])->name('uploadAndSign')->whereNumber('documentRequest');

        Route::match(['post', 'patch'], '/{documentRequest}/status', [AdminDocumentController::class, 'updateStatus'])->name('status')->whereNumber('documentRequest');
        Route::match(['post', 'patch'], '/{documentRequest}/update-status', [AdminDocumentController::class, 'updateStatus'])->name('update-status')->whereNumber('documentRequest');
        Route::get('/{documentRequest}/pdf', [AdminDocumentController::class, 'generatePdf'])->name('pdf')->whereNumber('documentRequest');
        Route::get('/{documentRequest}/download-word', [AdminDocumentController::class, 'downloadWord'])->name('download-word')->whereNumber('documentRequest');
        Route::match(['post', 'patch'], '/{documentRequest}/approve', [AdminDocumentController::class, 'updateStatus'])->name('approve')->whereNumber('documentRequest');
    });

    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [AdminIncidentController::class, 'index'])->name('index');
        Route::get('/{incidentReport}', [AdminIncidentController::class, 'show'])->name('show')->whereNumber('incidentReport');
        Route::match(['post', 'patch'], '/{incidentReport}/status', [AdminIncidentController::class, 'updateStatus'])->name('status')->whereNumber('incidentReport');
        Route::match(['post', 'patch'], '/{incidentReport}/update-status', [AdminIncidentController::class, 'updateStatus'])->name('update-status')->whereNumber('incidentReport');
    });

    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::get('/{facilityBooking}', [AdminBookingController::class, 'show'])->name('show')->whereNumber('facilityBooking');
        Route::patch('/{facilityBooking}/status', [AdminBookingController::class, 'updateStatus'])->name('update-status')->whereNumber('facilityBooking');
    });

    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AdminAnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AdminAnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AdminAnnouncementController::class, 'store'])->name('store');
        Route::get('/{announcement}', [AdminAnnouncementController::class, 'show'])->name('show')->whereNumber('announcement');
        Route::get('/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('edit')->whereNumber('announcement');
        Route::put('/{announcement}', [AdminAnnouncementController::class, 'update'])->name('update')->whereNumber('announcement');
        Route::delete('/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('destroy')->whereNumber('announcement');
    });

    Route::prefix('hotlines')->name('hotlines.')->group(function () {
        Route::get('/', [AdminHotlineController::class, 'index'])->name('index');
        Route::post('/', [AdminHotlineController::class, 'store'])->name('store');
        Route::put('/{hotline}', [AdminHotlineController::class, 'update'])->name('update')->whereNumber('hotline');
        Route::delete('/{hotline}', [AdminHotlineController::class, 'destroy'])->name('destroy')->whereNumber('hotline');
    });

    Route::middleware(['role:admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit')->whereNumber('user');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update')->whereNumber('user');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy')->whereNumber('user');
    });

    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

    Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('index');
        Route::put('/', [AdminSettingController::class, 'update'])->name('update');
    });

    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/{payment}/verify-offline', [PaymentDashboardController::class, 'verifyOfflinePayment'])->name('verify-offline');
        Route::post('/{payment}/refund', [PaymentDashboardController::class, 'processRefund'])->name('refund');
    });

});

require __DIR__.'/auth.php';