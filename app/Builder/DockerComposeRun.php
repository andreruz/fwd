<?php

namespace App\Builder;

class DockerComposeRun extends DockerComposeExec
{
    public function getProgramName() : string
    {
        return 'run';
    }

    protected function getEnvVar() : string
    {
        return env('FWD_COMPOSE_RUN_FLAGS');
    }

    protected function beforeBuild(Builder $command) : Builder
    {
        parent::beforeBuild($command);

        $command->prependArgument(new Argument('--rm'));

        return $command;
    }
}
