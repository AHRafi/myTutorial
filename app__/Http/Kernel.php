<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'superAdmin' => \App\Http\Middleware\SuperAdmin::class,
        'cmProfile' => \App\Http\Middleware\CmProfile::class,
        'ds' => \App\Http\Middleware\Ds::class,
        'ci' => \App\Http\Middleware\Ci::class,
        'comdt' => \App\Http\Middleware\Comdt::class,
        'superAdminCiComdt' => \App\Http\Middleware\SuperAdminCiComdt::class,
        'superAdminCi' => \App\Http\Middleware\SuperAdminCi::class,
        'deligatedDsCiComdtSuperAdmin' => \App\Http\Middleware\DeligatedDsCiComdtSuperAdmin::class,
        'deligatedDsCiSuperAdmin' => \App\Http\Middleware\DeligatedDsCiSuperAdmin::class,
        'ciComdt' => \App\Http\Middleware\CiComdt::class,
        'deligatedDsCiComdt' => \App\Http\Middleware\DeligatedDsCiComdt::class,
        'deligatedDsCi' => \App\Http\Middleware\DeligatedDsCi::class,
        'dsCiSuperAdmin' => \App\Http\Middleware\DsCiSuperAdmin::class,
        'dsCi' => \App\Http\Middleware\DsCi::class,
        'deligatedDsCiComdtSuperAdmin' => \App\Http\Middleware\DeligatedDsCiComdtSuperAdmin::class,
        'deligatedDsCiSuperAdmin' => \App\Http\Middleware\DeligatedDsCiSuperAdmin::class,
        'deligatedTermResultReport' => \App\Http\Middleware\DeligatedTermResultReport::class,
        'deligatedCourseResultReport' => \App\Http\Middleware\DeligatedCourseResultReport::class,
        'deligatedEventCombResultReport' => \App\Http\Middleware\DeligatedEventCombResultReport::class,
        'deligatedPerformanceAnalysisReport' => \App\Http\Middleware\DeligatedPerformanceAnalysisReport::class,
    ];
}
