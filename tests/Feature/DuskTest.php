<?php

namespace Tests\Feature;

use Tests\TestCase;

class DuskTest extends TestCase
{
    public function testDusk()
    {
        $this->artisan('dusk')->assertExitCode(0);

        $this->asFWDUser()->assertDockerComposeExec('app php artisan dusk');
    }
}
