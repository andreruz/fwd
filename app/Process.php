<?php

namespace App;

class Process
{
    protected $commands = [];
    protected $cwd = null;
    protected $asUser = null;
    protected $output = true;

    /** @var string $outputFileName */
    protected $outputFileName = '';

    /** @var string $errorFileName */
    protected $errorFileName = '';

    public function __destruct() {
        if ($this->outputFileName) {
            unlink($this->outputFileName);
        }

        if ($this->errorFileName) {
            unlink($this->errorFileName);
        }
    }

    public function setAsUser($user)
    {
        $this->asUser = $user;

        return $this;
    }

    public function asFWDUser()
    {
        return $this->setAsUser(env('FWD_ASUSER'));
    }

    public function enableOutput()
    {
        $this->output = true;

        return $this;
    }

    public function disableOutput()
    {
        $this->output = false;

        return $this;
    }

    public function dockerRun(...$command) : int
    {
        $commandPrefix = [
            'run --rm',
            env('FWD_DOCKER_RUN_FLAGS'),
            '-w /app',
            sprintf('-v %s:/app:cached', env('FWD_CONTEXT_PATH')),
            sprintf('-v %s:/home/developer/.ssh/id_rsa:cached', env('FWD_SSH_KEY_PATH')),
            sprintf('-e ASUSER=%s', env('FWD_ASUSER')),
        ];

        return $this->docker(...array_merge($commandPrefix, $command));
    }

    public function dockerComposeExec(...$command) : int
    {
        $params = [
            'exec',
        ];

        if (! empty($this->asUser)) {
            $params[] = '--user';
            $params[] = $this->asUser;
        }

        $params[] = env('FWD_COMPOSE_EXEC_FLAGS');

        return $this->dockerCompose(...$params, ...$command);
    }

    public function dockerComposeExecNoOutput(...$command) : int
    {
        $this->disableOutput();
        $exitCode = $this->dockerComposeExec(...$command);
        $this->enableOutput();

        if ($exitCode) {
            $this->print($this->getOutputBuffer());
            $this->print($this->getErrorBuffer());
        }

        return $exitCode;
    }

    public function docker(...$command) : int
    {
        $commandPrefix = [
            env('FWD_DOCKER_BIN', 'docker'),
        ];

        return $this->process(array_merge($commandPrefix, $command));
    }

    public function dockerNoOutput(...$command) : int
    {
        $this->disableOutput();
        $exitCode = $this->docker(...$command);
        $this->enableOutput();

        if ($exitCode) {
            $this->print($this->getOutputBuffer());
            $this->print($this->getErrorBuffer());
        }

        return $exitCode;
    }

    public function dockerCompose(...$command) : int
    {
        $commandPrefix = [
            env('FWD_DOCKER_COMPOSE_BIN', 'docker-compose'),
            sprintf('-p %s', env('FWD_NAME')),
        ];

        // @TODO: make docker-compose.yml optional
        // $environment = app(Environment::class);
        // if (! File::exists($environment->getContextDockerCompose())) {
        //     $commandPrefix[] = sprintf('-f %s', $environment->getDefaultDockerCompose());
        // }

        return $this->process(array_merge($commandPrefix, $command));
    }

    public function process(array $command, string $cwd = null) : int
    {
        $command = $this->buildCommand($command);

        $this->commands[] = $command;

        if (env('FWD_DEBUG') || env('FWD_VERBOSE')) {
            $this->print($command);
        }

        if (env('FWD_DEBUG')) {
            return 0;
        }

        return $this->run($command, $cwd);
    }

    public function commands()
    {
        return $this->commands;
    }

    public function cwd(string $cwd)
    {
        return $this->cwd = $cwd;

        return $this;
    }

    public function hasCommand($command)
    {
        return array_search($command, $this->commands) !== false;
    }

    protected function buildCommand(array $command)
    {
        return trim(implode(' ', array_filter($command)));
    }

    protected function run(string $command) : int
    {
        $pipes = [];
        $proc = proc_open(
            $command,
            $this->getDescriptors(),
            $pipes,
            $this->cwd,
            null,
            []
        );

        return proc_close($proc);
    }

    public function getOutputBuffer(): string
    {
        return $this->getFileContents($this->outputFileName);
    }

    public function getErrorBuffer(): string
    {
        return $this->getFileContents($this->errorFileName);
    }

    private function getFileContents(string $filename): string
    {
        $output = '';
        $handle = @fopen($filename, "r");

        while (($buffer = fgets($handle)) !== false) {
            $output .= trim($buffer) . "\n";
        }

        if (!feof($handle)) {
            $output = "Erro: falha inexperada na leitura do arquivo!";
        }

        return $output;
    }

    protected function getDescriptors() : array
    {
        if ($this->output || env('FWD_VERBOSE')) {
            return [STDIN, STDOUT, STDERR];
        }

        $this->outputFileName = @tempnam(sys_get_temp_dir(), 'fwd_output_');
        $this->errorFileName = @tempnam(sys_get_temp_dir(), 'fwd_error_');

        return [
            STDIN,
            [
                'file',
                $this->outputFileName,
                'w',
            ],
            [
                'file',
                $this->errorFileName,
                'a',
            ],
        ];
    }

    protected function print($line)
    {
        if (empty($line)) {
            return;
        }

        echo $line . PHP_EOL;
    }
}
