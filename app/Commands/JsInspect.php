<?php

namespace App\Commands;

use App\Builder\NodeQa;
use App\Commands\Traits\HasDynamicArgs;

class JsInspect extends Command
{
    use HasDynamicArgs;

    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'jsinspect';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run jsinspect in the NODE-QA container.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->commandExecutor->run(
            NodeQa::make('jsinspect', $this->getArgs())
        );
    }

    /**
     * Get default args when empty.
     *
     * @return string
     */
    public function getDefaultArgs(): string
    {
        return 'src/';
    }
}
