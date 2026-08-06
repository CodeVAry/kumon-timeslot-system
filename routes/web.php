<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionOfferingController;
use App\Http\Controllers\Admin\StudentStatusController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| A registered user without a role can access the dashboard.
| The dashboard will show the waiting-for-role-assignment message.
|
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Every admin route requires authentication.
| Each action also requires its related permission.
|
*/

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Role Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/roles',
            [RoleController::class, 'index']
        )
            ->middleware('permission:roles.view')
            ->name('roles.index');

        Route::get(
            '/roles/create',
            [RoleController::class, 'create']
        )
            ->middleware('permission:roles.create')
            ->name('roles.create');

        Route::post(
            '/roles',
            [RoleController::class, 'store']
        )
            ->middleware('permission:roles.create')
            ->name('roles.store');

        Route::get(
            '/roles/{role}/edit',
            [RoleController::class, 'edit']
        )
            ->middleware('permission:roles.edit')
            ->name('roles.edit');

        Route::put(
            '/roles/{role}',
            [RoleController::class, 'update']
        )
            ->middleware('permission:roles.edit')
            ->name('roles.update');

        Route::delete(
            '/roles/{role}',
            [RoleController::class, 'destroy']
        )
            ->middleware('permission:roles.delete')
            ->name('roles.destroy');


        /*
        |--------------------------------------------------------------------------
        | Permission Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/permissions',
            [PermissionController::class, 'index']
        )
            ->middleware('permission:permissions.view')
            ->name('permissions.index');

        Route::put(
            '/permissions/{role}',
            [PermissionController::class, 'update']
        )
            ->middleware('permission:permissions.edit')
            ->name('permissions.update');


        /*
        |--------------------------------------------------------------------------
        | User Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users',
            [UserController::class, 'index']
        )
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get(
            '/users/create',
            [UserController::class, 'create']
        )
            ->middleware('permission:users.create')
            ->name('users.create');

        Route::post(
            '/users',
            [UserController::class, 'store']
        )
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::get(
            '/users/{user}/edit',
            [UserController::class, 'edit']
        )
            ->middleware('permission:users.edit')
            ->name('users.edit');

        Route::put(
            '/users/{user}',
            [UserController::class, 'update']
        )
            ->middleware('permission:users.edit')
            ->name('users.update');

        Route::delete(
            '/users/{user}',
            [UserController::class, 'destroy']
        )
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        /*
        |--------------------------------------------------------------------------
        | Section Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/sections',
            [SectionController::class, 'index']
        )
            ->middleware('permission:sections.view')
            ->name('sections.index');

        Route::get(
            '/sections/create',
            [SectionController::class, 'create']
        )
            ->middleware('permission:sections.create')
            ->name('sections.create');

        Route::post(
            '/sections',
            [SectionController::class, 'store']
        )
            ->middleware('permission:sections.create')
            ->name('sections.store');

        Route::get(
            '/sections/{section}/edit',
            [SectionController::class, 'edit']
        )
            ->middleware('permission:sections.edit')
            ->name('sections.edit');

        Route::put(
            '/sections/{section}',
            [SectionController::class, 'update']
        )
            ->middleware('permission:sections.edit')
            ->name('sections.update');

        Route::delete(
            '/sections/{section}',
            [SectionController::class, 'destroy']
        )
            ->middleware('permission:sections.delete')
            ->name('sections.destroy');

        /*
|--------------------------------------------------------------------------
| Section Offering Routes
|--------------------------------------------------------------------------
*/

        Route::get(
            '/section-offerings',
            [SectionOfferingController::class, 'index']
        )
            ->middleware('permission:section_offerings.view')
            ->name('section-offerings.index');

        Route::get(
            '/section-offerings/create',
            [SectionOfferingController::class, 'create']
        )
            ->middleware('permission:section_offerings.create')
            ->name('section-offerings.create');

        Route::post(
            '/section-offerings',
            [SectionOfferingController::class, 'store']
        )
            ->middleware('permission:section_offerings.create')
            ->name('section-offerings.store');

        Route::get(
            '/section-offerings/{sectionOffering}/edit',
            [SectionOfferingController::class, 'edit']
        )
            ->middleware('permission:section_offerings.edit')
            ->name('section-offerings.edit');

        Route::put(
            '/section-offerings/{sectionOffering}',
            [SectionOfferingController::class, 'update']
        )
            ->middleware('permission:section_offerings.edit')
            ->name('section-offerings.update');

        Route::delete(
            '/section-offerings/{sectionOffering}',
            [SectionOfferingController::class, 'destroy']
        )
            ->middleware('permission:section_offerings.delete')
            ->name('section-offerings.destroy');

        /*
|--------------------------------------------------------------------------
| Student Status Routes
|--------------------------------------------------------------------------
*/

        Route::get(
            '/student-statuses',
            [StudentStatusController::class, 'index']
        )
            ->middleware('permission:student_statuses.view')
            ->name('student-statuses.index');

        Route::get(
            '/student-statuses/create',
            [StudentStatusController::class, 'create']
        )
            ->middleware('permission:student_statuses.create')
            ->name('student-statuses.create');

        Route::post(
            '/student-statuses',
            [StudentStatusController::class, 'store']
        )
            ->middleware('permission:student_statuses.create')
            ->name('student-statuses.store');

        Route::get(
            '/student-statuses/{studentStatus}/edit',
            [StudentStatusController::class, 'edit']
        )
            ->middleware('permission:student_statuses.edit')
            ->name('student-statuses.edit');

        Route::put(
            '/student-statuses/{studentStatus}',
            [StudentStatusController::class, 'update']
        )
            ->middleware('permission:student_statuses.edit')
            ->name('student-statuses.update');

        Route::delete(
            '/student-statuses/{studentStatus}',
            [StudentStatusController::class, 'destroy']
        )
            ->middleware('permission:student_statuses.delete')
            ->name('student-statuses.destroy');

    });


/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
