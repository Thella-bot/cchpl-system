<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected function runningUnitTests(): bool
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }
}
