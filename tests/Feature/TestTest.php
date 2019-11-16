<?php

namespace Tests\Feature;

use App\Environment;
use Tests\TestCase;

class TestTest extends TestCase
{
    public function testTest()
    {
        $this->artisan('test')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/phpunit');
    }

    public function testTestWithFilter()
    {
        $this->artisan('test --filter=something')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/phpunit --filter=something');
    }

    public function testTestingWithDockerComposeFlags()
    {
        app(Environment::class)->overloadEnv('tests/fixtures/.env.docker-compose_exec');

        $this->artisan('test')->assertExitCode(0);

        $this->asFwdUser()->assertDockerComposeExec('app php vendor/bin/phpunit');
    }
}
