<?php

namespace App\Commands;

use App\Commands\Traits\HasDynamicArgs;
use App\Builder\Docker as DockerBuilder;

class Docker extends Command
{
    use HasDynamicArgs;

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'docker';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run docker directly.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->commandExecutor->run(
            DockerBuilder::makeWithDefaultArgs($this->getArgs())
        );
    }
}
