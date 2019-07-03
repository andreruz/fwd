<?php

namespace App\Commands;

use App\Builder\Docker;
use App\CommandExecutor;
use App\Commands\Traits\RunTask;
use LaravelZero\Framework\Commands\Command;

class CheckDockerVersion extends Command
{
    const DOCKER_MIN_VERSION = '18.09';

    use RunTask;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'check-docker-version';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Checks that the docker version is compatible';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CommandExecutor $executor)
    {
        return $this->runTask('Checking Docker version', function () use ($executor) {
            return $this->checkDockerVersion($executor);
        });
    }

    protected function checkDockerVersion(CommandExecutor $executor): int
    {
        $exitCode = $executor->runQuietly(new Docker("version --format '{{.Server.Version}}'"));

        $output = $executor->getOutputBuffer();

        $isValidVersion = $exitCode === 0 && $output && version_compare($output, self::DOCKER_MIN_VERSION, '>=');

        if (! $isValidVersion) {
            $this->error('Docker version must be >= ' . self::DOCKER_MIN_VERSION);
        }

        return $isValidVersion ? 0 : 1;
    }
}
