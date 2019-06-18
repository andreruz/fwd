<?php

namespace App\Builder;

class Artisan extends Command
{
    public function getProgramName()
    {
        return 'app php artisan';
    }

    public function makeWrapper() : ?Command
    {
        return (new DockerComposeExec())->setUser(env('FWD_ASUSER'));
    }

    public function getDockerComposeExec() : DockerComposeExec
    {
        return $this->wrapper;
    }
}
