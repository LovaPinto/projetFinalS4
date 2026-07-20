<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\InvalidChars;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'invalidchars'  => InvalidChars::class,
        'forcehttps'    => ForceHTTPS::class,
        'clientAuth'    => \App\Filters\ClientAuthFilter::class,
        'operatorAuth'  => \App\Filters\OperatorAuthFilter::class,
    ];

    public array $required = [
        'before' => [
            'forcehttps',
        ],
        'after' => [
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [],
        'after'  => [],
    ];

    public array $methods = [];

    public array $filters = [];
}
