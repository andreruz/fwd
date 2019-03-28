<?php

namespace Tests\Feature;

use Tests\TestCase;

class StopTest extends TestCase
{
    public function testStop()
    {
        $this->artisan('stop')->assertExitCode(0);

        $this->assertDockerCompose('stop');
    }

    public function testStopCustom()
    {
        $this->artisan('stop -t 5')->assertExitCode(0);

        $this->assertDockerCompose('stop -t 5');
    }
}
