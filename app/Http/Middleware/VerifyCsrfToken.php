<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Exclude testing routes from CSRF verification
        'admin/lessons/*',
        'admin/users/*',
        'admin/school-classes/*',
        'admin/rooms/*',
        'admin/subjects/*',
        'admin/academic-programs/*',
    ];
}
