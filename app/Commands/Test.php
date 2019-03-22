<?php

namespace App\Commands;

use App\Commands\Traits\HasDynamicArgs;
use App\Process;
use LaravelZero\Framework\Commands\Command;

class Test extends Command
{
    use HasDynamicArgs;

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'test';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run phpunit commands in the APP container.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Process $process)
    {
        $process->dockerCompose('exec app ./vendor/bin/phpunit', $this->getArgs());
    }
}
