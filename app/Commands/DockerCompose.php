<?php

namespace App\Commands;

use App\Commands\Traits\HasDynamicArgs;
use App\Builder\DockerCompose as DockerComposeBuilder;

class DockerCompose extends Command
{
    use HasDynamicArgs;

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'docker-compose';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run docker-compose directly.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->commandExecutor->run(
            DockerComposeBuilder::makeWithDefaultArgs($this->getArgs())
        );
    }
}
