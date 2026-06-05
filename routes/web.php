<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CommunicationController;
use App\Http\Controllers\Admin\StoryPointController;
use App\Http\Controllers\Admin\WorkloadController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get(
    '/login',
    [AuthController::class,'showLogin']
)->name('login');

Route::post(
    '/login',
    [AuthController::class,'login']
)->name('login.process');

Route::post(
    '/logout',
    [AuthController::class,'logout']
)->name('logout');

Route::redirect('/', '/login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot.password');
/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->group(function () {
        Route::get(
            '/dashboard',
            [DashboardController::class,'index']
        )->name('dashboard');

        Route::resource(
            'members',
            MemberController::class
        );

        Route::resource(
            'communication',
            CommunicationController::class
        );

        Route::resource(
            'story-points',
            StoryPointController::class
        );

        Route::resource(
            'workload',
            WorkloadController::class
        );

        /*
        |--------------------------------------------------------------------------
        | COMMUNICATION
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/communication/period/store',
            [CommunicationController::class,'storePeriod']
        )->name('communication.period.store');

        Route::delete(
            '/communication/period/delete/{id}',
            [CommunicationController::class,'destroyPeriod']
        )->name('communication.period.destroy');

        /*
        |--------------------------------------------------------------------------
        | STORY POINT
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/story-points/period/store',
            [StoryPointController::class,'storePeriod']
        )->name('story-points.period.store');

        Route::delete(
            '/story-points/period/delete/{id}',
            [StoryPointController::class,'destroyPeriod']
        )->name('story-points.period.destroy');

        /*
        |--------------------------------------------------------------------------
        | WORKLOAD
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/workload/period/store',
            [WorkloadController::class,'storePeriod']
        )->name('workload.period.store');

        Route::delete(
            '/workload/period/delete/{id}',
            [WorkloadController::class,'destroyPeriod']
        )->name('workload.period.destroy');

    });