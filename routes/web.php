<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SectionOfferingController;
use App\Http\Controllers\Admin\StudentStatusController;
use App\Http\Controllers\Admin\StudentRegistrationController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentEnrolmentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\WishlistController;
use App\Http\Controllers\Parent\Auth\ParentLoginController;
use App\Http\Controllers\Parent\ParentPortalController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
|
| A registered user without a role can access the dashboard.
| The dashboard will show the waiting-for-role-assignment message.
|
*/

Route::get(
    '/dashboard',
    [
        DashboardController::class,
        'index',
    ]
)
    ->middleware(['auth'])
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

        /*
|--------------------------------------------------------------------------
| Student Registration Wizard
|--------------------------------------------------------------------------
*/

        Route::get(
            '/student-registration',
            [
                StudentRegistrationController::class,
                'studentStep',
            ]
        )
            ->middleware('permission:students.create')
            ->name('student-registration.student');

        Route::post(
            '/student-registration/student',
            [
                StudentRegistrationController::class,
                'storeStudentStep',
            ]
        )
            ->middleware('permission:students.create')
            ->name('student-registration.student.store');

        Route::get(
            '/student-registration/guardians',
            [
                StudentRegistrationController::class,
                'guardianStep',
            ]
        )
            ->middleware('permission:students.create')
            ->name('student-registration.guardians');

        Route::post(
            '/student-registration/guardians',
            [
                StudentRegistrationController::class,
                'storeGuardianStep',
            ]
        )
            ->middleware('permission:students.create')
            ->name('student-registration.guardians.store');

        Route::get(
            '/student-registration/enrolments',
            [
                StudentRegistrationController::class,
                'enrolmentStep',
            ]
        )
            ->middleware([
                'permission:students.create',
                'permission:enrolments.create',
            ])
            ->name('student-registration.enrolments');

        Route::post(
            '/student-registration/cancel',
            [
                StudentRegistrationController::class,
                'cancel',
            ]
        )
            ->middleware('permission:students.create')
            ->name('student-registration.cancel');

        Route::post(
            '/student-registration/complete',
            [
                StudentRegistrationController::class,
                'complete',
            ]
        )
            ->middleware([
                'permission:students.create',
                'permission:enrolments.create',
            ])
            ->name('student-registration.complete');

        Route::get(
            '/students',
            [StudentController::class, 'index']
        )
            ->middleware(
                'permission:students.view'
            )
            ->name('students.index');


        Route::get(
            '/students/{student}',
            [StudentController::class, 'show']
        )
            ->middleware(
                'permission:students.view'
            )
            ->name('students.show');


        Route::get(
            '/students/{student}/edit',
            [StudentController::class, 'edit']
        )
            ->middleware(
                'permission:students.edit'
            )
            ->name('students.edit');


        Route::patch(
            '/students/{student}',
            [StudentController::class, 'update']
        )
            ->middleware(
                'permission:students.edit'
            )
            ->name('students.update');

        Route::get(
            '/students/{student}/classes/add',
            [
                StudentEnrolmentController::class,
                'create',
            ]
        )
            ->middleware(
                'permission:enrolments.create'
            )
            ->name(
                'student-enrolments.create'
            );


        Route::post(
            '/students/{student}/classes',
            [
                StudentEnrolmentController::class,
                'store',
            ]
        )
            ->middleware(
                'permission:enrolments.create'
            )
            ->name(
                'student-enrolments.store'
            );


        Route::get(
            '/students/{student}/classes/edit',
            [
                StudentEnrolmentController::class,
                'editList',
            ]
        )
            ->middleware(
                'permission:enrolments.edit'
            )
            ->name(
                'student-enrolments.edit-list'
            );


        Route::get(
            '/students/{student}/classes/{enrolment}/edit',
            [
                StudentEnrolmentController::class,
                'edit',
            ]
        )
            ->middleware(
                'permission:enrolments.edit'
            )
            ->name(
                'student-enrolments.edit'
            );


        Route::patch(
            '/students/{student}/classes/{enrolment}',
            [
                StudentEnrolmentController::class,
                'update',
            ]
        )
            ->middleware(
                'permission:enrolments.edit'
            )
            ->name(
                'student-enrolments.update'
            );

        Route::get(
            '/students/{student}/classes/remove',
            [
                StudentEnrolmentController::class,
                'remove',
            ]
        )
            ->middleware(
                'permission:enrolments.delete'
            )
            ->name(
                'student-enrolments.remove'
            );

        Route::delete(
            '/students/{student}/classes/{enrolment}',
            [
                StudentEnrolmentController::class,
                'destroy',
            ]
        )
            ->middleware(
                'permission:enrolments.delete'
            )
            ->name(
                'student-enrolments.destroy'
            );

        Route::get(
            '/section-offerings/selected/view',
            [
                SectionOfferingController::class,
                'selectedOffering',
            ]
        )
            ->middleware(
                'permission:section_offerings.view'
            )
            ->name(
                'section-offerings.selected'
            );

        Route::get(
            '/schedule',
            [
                ScheduleController::class,
                'index',
            ]
        )
            ->middleware(
                'permission:section_offerings.view'
            )
            ->name(
                'schedule.index'
            );


        Route::get(
            '/schedule/print-day',
            [
                ScheduleController::class,
                'printDay',
            ]
        )
            ->middleware(
                'permission:enrolments.print'
            )
            ->name(
                'schedule.print-day'
            );


        Route::get(
            '/schedule/classes/{sectionOffering}/students/print',
            [
                ScheduleController::class,
                'printClass',
            ]
        )
            ->middleware(
                'permission:enrolments.print'
            )
            ->name(
                'schedule.class-students.print'
            );


        Route::get(
            '/schedule/classes/{sectionOffering}/students',
            [
                ScheduleController::class,
                'classStudents',
            ]
        )
            ->middleware(
                'permission:students.view'
            )
            ->name(
                'schedule.class-students'
            );
        Route::get(
            '/attendance',
            [
                AttendanceController::class,
                'index',
            ]
        )
            ->name(
                'attendance.index'
            );


        Route::get(
            '/attendance/classes/{sectionOffering}',
            [
                AttendanceController::class,
                'takeAttendance',
            ]
        )
            ->name(
                'attendance.takeAttendance'
            );


        Route::post(
            '/attendance/classes/{sectionOffering}',
            [
                AttendanceController::class,
                'store',
            ]
        )
            ->name(
                'attendance.store'
            );

        Route::get(
            '/leave',
            [
                LeaveController::class,
                'index',
            ]
        )
            ->middleware('permission:leave.view')
            ->name('leave.index');


        Route::get(
            '/leave/create',
            [
                LeaveController::class,
                'create',
            ]
        )
            ->middleware('permission:leave.create')
            ->name('leave.create');


        Route::post(
            '/leave',
            [
                LeaveController::class,
                'store',
            ]
        )
            ->middleware('permission:leave.create')
            ->name('leave.store');


        Route::get(
            '/leave/history',
            [
                LeaveController::class,
                'history',
            ]
        )
            ->middleware('permission:leave.view')
            ->name('leave.history');


        Route::get(
            '/leave/{leave}',
            [
                LeaveController::class,
                'show',
            ]
        )
            ->middleware('permission:leave.view')
            ->name('leave.show');


        Route::get(
            '/leave/{leave}/edit',
            [
                LeaveController::class,
                'edit',
            ]
        )
            ->middleware('permission:leave.edit')
            ->name('leave.edit');


        Route::patch(
            '/leave/{leave}',
            [
                LeaveController::class,
                'update',
            ]
        )
            ->middleware('permission:leave.edit')
            ->name('leave.update');


        Route::patch(
            '/leave/{leave}/return',
            [
                LeaveController::class,
                'returnStudent',
            ]
        )
            ->middleware('permission:leave.edit')
            ->name('leave.return');

        Route::get(
            '/wishlist',
            [
                WishlistController::class,
                'index',
            ]
        )
            ->middleware(
                'permission:wishlist.view'
            )
            ->name(
                'wishlist.index'
            );


        Route::get(
            '/wishlist/create/{enrolment}',
            [
                WishlistController::class,
                'create',
            ]
        )
            ->middleware(
                'permission:wishlist.create'
            )
            ->name(
                'wishlist.create'
            );


        Route::post(
            '/wishlist/{enrolment}',
            [
                WishlistController::class,
                'store',
            ]
        )
            ->middleware(
                'permission:wishlist.create'
            )
            ->name(
                'wishlist.store'
            );


        Route::patch(
            '/wishlist/{wishlist}/approve',
            [
                WishlistController::class,
                'approve',
            ]
        )
            ->middleware(
                'permission:wishlist.edit'
            )
            ->name(
                'wishlist.approve'
            );


        Route::patch(
            '/wishlist/{wishlist}/cancel',
            [
                WishlistController::class,
                'cancel',
            ]
        )
            ->middleware(
                'permission:wishlist.edit'
            )
            ->name(
                'wishlist.cancel'
            );

    });

/*
|--------------------------------------------------------------------------
| Parent Portal Routes
|--------------------------------------------------------------------------
*/

Route::prefix('parent')
    ->name('parent.')
    ->group(function () {

        Route::get(
            '/login',
            [
                ParentLoginController::class,
                'showLogin'
            ]
        )->name('login');


        Route::post(
            '/login',
            [
                ParentLoginController::class,
                'checkLogin'
            ]
        )->name('login.check');


        Route::get(
            '/verify-otp',
            [
                ParentLoginController::class,
                'showOtp'
            ]
        )->name('otp');


        Route::post(
            '/verify-otp',
            [
                ParentLoginController::class,
                'verifyOtp'
            ]
        )->name('otp.verify');
        
        Route::post(
            '/logout',
            [
                ParentLoginController::class,
                'logout'
            ]
        )->name('logout');

    });

Route::prefix('parent')
    ->name('parent.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Guest Parent Routes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/login',
            [
                ParentLoginController::class,
                'showLogin'
            ]
        )->name('login');


        Route::post(
            '/login',
            [
                ParentLoginController::class,
                'checkLogin'
            ]
        )->name('login.check');


        Route::get(
            '/verify-otp',
            [
                ParentLoginController::class,
                'showOtp'
            ]
        )->name('otp');


        Route::post(
            '/verify-otp',
            [
                ParentLoginController::class,
                'verifyOtp'
            ]
        )->name('otp.verify');



        /*
        |--------------------------------------------------------------------------
        | Logged-in Parent Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware(
            'auth:parent'
        )->group(function () {

            Route::get(
                '/welcome',
                [
                    ParentPortalController::class,
                    'welcome'
                ]
            )->name('welcome');


            Route::post(
                '/students/{student}/select',
                [
                    ParentPortalController::class,
                    'selectStudent'
                ]
            )->name(
                    'students.select'
                );


            Route::get(
                '/dashboard',
                [
                    ParentPortalController::class,
                    'dashboard'
                ]
            )->name(
                    'dashboard'
                );

        });

    });


/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
