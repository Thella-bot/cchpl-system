<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->app->runningInConsole() && $this->app->runningUnitTests()) {
            $this->withoutMiddleware(VerifyCsrfToken::class);
        }
    }

    public function artisan($command, $parameters = [], $newOutput = true)
    {
        if ($command === 'migrate:fresh') {
            $parameters['--force'] = true;
        }

        return parent::artisan($command, $parameters, $newOutput);
    }
}
