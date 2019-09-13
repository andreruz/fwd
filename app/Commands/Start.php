<?php

namespace App\Commands;

use App\Tasks\Start as StartTask;

class Start extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start
                            {--no-wait : Do not wait for Docker and MySQL to become available}
                            {--timeout=60 : The number of seconds to wait}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Start fwd environment containers.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timeout = ! $this->option('no-wait') ? $this->option('timeout') : 0;

        return StartTask::make($this)->timeout($timeout)->run();
    }
}
