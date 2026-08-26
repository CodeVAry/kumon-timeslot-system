<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Admin\Enrolment;
use App\Models\Admin\Guardian;
use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\Student;
use App\Models\Admin\StudentStatus;
use App\Observers\AuditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Student::observe(AuditObserver::class);

        Guardian::observe(AuditObserver::class);

        Enrolment::observe(AuditObserver::class);

        Section::observe(AuditObserver::class);

        SectionOffering::observe(AuditObserver::class);

        StudentStatus::observe(AuditObserver::class);

        User::observe(AuditObserver::class);

        Role::observe(AuditObserver::class);

        Permission::observe(AuditObserver::class);
    }
}
