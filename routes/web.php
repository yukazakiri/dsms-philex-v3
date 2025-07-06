<?php

declare(strict_types=1);

use App\Http\Controllers\ApkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// PWA Mobile Entry Point
Route::get('/mobile', function () {
    return response()->file(public_path('mobile/index.html'));
})->name('mobile');

// PWA Downloads Page with APK download support
Route::get('/downloads', function (Request $request) {
    // Check if APK download is requested
    if ($request->has('apk') || $request->has('download')) {
        $apkPath = storage_path('app/public/apk/dsms-philex.apk');

        if (!file_exists($apkPath)) {
            abort(404, 'APK file not found');
        }

        $fileName = 'dsms-philex-v' . config('app.apk_version', '1.0.0') . '.apk';

        return response()->download($apkPath, $fileName, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => filesize($apkPath),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    // Default: serve the downloads page
    return response()->file(public_path('downloads/index.html'));
})->name('downloads');

// Direct APK Download Route
Route::get('/download-apk', function () {
    $apkPath = storage_path('app/public/apk/dsms-philex.apk');

    if (!file_exists($apkPath)) {
        abort(404, 'APK file not found');
    }

    $fileName = 'dsms-philex-v' . config('app.apk_version', '1.0.0') . '.apk';

    return response()->download($apkPath, $fileName, [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Content-Length' => filesize($apkPath),
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->name('download.apk');

// APK Management Routes
Route::get('/apk/test', function () {
    return response()->json(['status' => 'APK routes working', 'timestamp' => now()]);
});

Route::prefix('apk')->name('apk.')->group(function () {
    Route::get('/info', [ApkController::class, 'getApkInfo'])->name('info');
    Route::get('/download', [ApkController::class, 'downloadApk'])->name('download');
    Route::get('/check-update', [ApkController::class, 'checkUpdate'])->name('check-update');
    Route::get('/download-page', [ApkController::class, 'downloadPage'])->name('download-page');

    // Admin only routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/upload', [ApkController::class, 'uploadApk'])->name('upload');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'student') {
            return redirect()->route('student.dashboard');
        }
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Fallback to default dashboard
        return Inertia::render('dashboard');

    })->name('dashboard');

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::post('/apply-oauth-avatar', [ProfileController::class, 'applyOAuthAvatar'])->name('apply-oauth-avatar');
        Route::post('/apply-facebook-avatar', [ProfileController::class, 'applyOAuthAvatar'])->name('apply-facebook-avatar'); // Backward compatibility
        Route::post('/update-avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::post('/update-cover-image', [ProfileController::class, 'updateCoverImage'])->name('update-cover-image');
    });

    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
        Route::delete('/', [NotificationController::class, 'deleteAll'])->name('deleteAll');
    });

    // Test routes
    Route::get('/test-notifications', function () {
        return Inertia::render('test-notifications');
    })->name('test-notifications');

    Route::post('/test-notification', function () {
        $user = Auth::user();
        $user->notify(new App\Notifications\DatabaseNotification(
            request('title', 'Test Notification'),
            request('message', 'This is a test'),
            request('type', 'info')
        ));

        return back()->with('success', 'Test notification sent!');
    })->name('test-notification');

    // Test routes for flash messages
    Route::post('/test-flash/success', function () {
        return back()->with('success', 'This is a success message!');
    })->name('test-flash.success');

    Route::post('/test-flash/error', function () {
        return back()->with('error', 'This is an error message!');
    })->name('test-flash.error');

    Route::post('/test-flash/warning', function () {
        return back()->with('warning', 'This is a warning message!');
    })->name('test-flash.warning');

    Route::post('/test-flash/info', function () {
        return back()->with('info', 'This is an info message!');
    })->name('test-flash.info');

    Route::post('/test-flash/status', function () {
        return back()->with('status', 'This is a status message!');
    })->name('test-flash.status');
});

// Development-only quick login routes
if (app()->environment('local', 'development')) {
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('/login/admin', function () {
            Auth::loginUsingId(1); // Admin user ID from seeder
            return redirect()->route('dashboard');
        })->name('login.admin');

        Route::get('/login/student', function () {
            Auth::loginUsingId(2); // Student user ID from seeder (John Doe)
            return redirect()->route('dashboard');
        })->name('login.student');

        Route::get('/login/jane', function () {
            Auth::loginUsingId(3); // Jane Smith user ID from seeder
            return redirect()->route('dashboard');
        })->name('login.jane');

        Route::get('/login/bob', function () {
            Auth::loginUsingId(4); // Bob Johnson user ID from seeder
            return redirect()->route('dashboard');
        })->name('login.bob');
    });
}

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/student.php';
require __DIR__.'/admin.php';
