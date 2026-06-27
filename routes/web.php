<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;


/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\StoryPointController;
use App\Http\Controllers\Admin\WorkloadController;
use App\Http\Controllers\Admin\PerformanceInsightController;
use App\Http\Controllers\Admin\ApprovalController;

/*
|--------------------------------------------------------------------------
| MEMBER CONTROLLERS
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\PerformanceInsightController as MemberPerformanceInsightController;
use App\Http\Controllers\Member\ProfileController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/forgot-password',       [PasswordResetController::class, 'showForgot'])->name('forgot.password');
Route::post('/forgot-password',      [PasswordResetController::class, 'sendResetLink'])->name('password.send-link');
Route::post('/reset-password',       [PasswordResetController::class, 'resetPassword'])->name('password.reset');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset.form');

Route::middleware('auth')->group(function () {
    Route::get('/notifications',             [NotificationController::class, 'page'])->name('notifications.index');
    Route::post('/notifications/{id}/read',  [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all',   [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

// ADMIN — tambah names khusus untuk members saja
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::resource('members', MemberController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names([                                    // ← tambah ini
                'index'   => 'admin.members.index',
                'store'   => 'admin.members.store',
                'update'  => 'admin.members.update',
                'destroy' => 'admin.members.destroy',
            ]);

        Route::get('/members/export',   [MemberController::class, 'export'])->name('admin.members.export');
        Route::post('/members/import',  [MemberController::class, 'import'])->name('admin.members.import');
        Route::get('/members/template', [MemberController::class, 'downloadTemplate'])->name('admin.members.template');
        Route::get('/members/check-name', [MemberController::class, 'checkName'])->name('admin.members.checkName');

    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        /*
        | DASHBOARD
        */
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


        /*
        | COMMUNICATION
        */
        Route::resource('communication', CommunicationController::class)->names([
            'index'   => 'admin.communication.index',
            'store'   => 'admin.communication.store',
            'update'  => 'admin.communication.update',
            'destroy' => 'admin.communication.destroy',
        ]);
        Route::post('/communication/period/store',       [CommunicationController::class, 'storePeriod'])->name('communication.period.store');
        Route::delete('/communication/period/delete/{id}', [CommunicationController::class, 'destroyPeriod'])->name('communication.period.destroy');

        /*
        | STORY POINTS
        */
        Route::resource('story-points', StoryPointController::class)->names([
            'index'   => 'admin.story-points.index',
            'store'   => 'admin.story-points.store',
            'update'  => 'admin.story-points.update',
            'destroy' => 'admin.story-points.destroy',
        ]);
        Route::post('/story-points/period/store',       [StoryPointController::class, 'storePeriod'])->name('story-points.period.store');
        Route::delete('/story-points/period/delete/{id}', [StoryPointController::class, 'destroyPeriod'])->name('story-points.period.destroy');

        /*
        | WORKLOAD
        */
       Route::resource('workload', WorkloadController::class)->names([
            'index'   => 'admin.workload.index',
            'store'   => 'admin.workload.store',
            'update'  => 'admin.workload.update',
            'destroy' => 'admin.workload.destroy',
        ]);
        Route::post('/workload/period/store',       [WorkloadController::class, 'storePeriod'])->name('workload.period.store');
        Route::delete('/workload/period/delete/{id}', [WorkloadController::class, 'destroyPeriod'])->name('workload.period.destroy');

        /*
        | PERFORMANCE INSIGHT
        */
        Route::get('/performance-insight',         [PerformanceInsightController::class, 'index'])->name('performance-insight.index');
        Route::post('/performance-insight/generate', [PerformanceInsightController::class, 'generate'])->name('performance-insight.generate');
        Route::post('/performance-insight/send',     [PerformanceInsightController::class, 'send'])->name('performance-insight.send');

        /*
        | APPROVALS
        */
        Route::get('/approvals',              [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/approve/{id}', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/reject/{id}',  [ApprovalController::class, 'reject'])->name('approvals.reject');

    });

/*
|--------------------------------------------------------------------------
| MEMBER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:member'])
    ->prefix('member')
    ->group(function () {

        /*
        | DASHBOARD
        */
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');

        /*
        | MEMBERS
        */
        Route::resource('members', \App\Http\Controllers\Member\MemberController::class)->only(['index', 'store']);
        Route::get('/members/export', [\App\Http\Controllers\Admin\MemberController::class, 'export'])->name('members.export');


        /*
        | METRICS
        */
        Route::resource('communication', \App\Http\Controllers\Member\CommunicationController::class);
        Route::resource('story-points',  \App\Http\Controllers\Member\StoryPointController::class);
        Route::resource('workload',      \App\Http\Controllers\Member\WorkloadController::class);
        Route::get( '/performance-insight', [MemberPerformanceInsightController::class,'index'] )->name('member.performance-insight.index'
        );

        /*
        | PROFILE
        */
        Route::get('/profile', [\App\Http\Controllers\Member\ProfileController::class, 'index'])->name('member.profile');
        Route::put('/profile', [\App\Http\Controllers\Member\ProfileController::class, 'update'])->name('member.profile.update');
            
        });