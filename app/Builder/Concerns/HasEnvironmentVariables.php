<?php

namespace App\Builder\Concerns;

use App\Builder\Argument;
use App\Builder\Unescaped;

trait HasEnvironmentVariables
{
    /** @var array $environment */
    protected $environment = [];

    public function setEnvs(array $envs) : self
    {
        $this->environment = [];

        foreach ($envs as $key => $value) {
            $this->addEnv($key, $value);
        }

        return $this;
    }

    public function addEnv($var, $value = null)
    {
        $this->appendEnv(new Argument($var, $value));

        return $this;
    }

    public function appendEnv(Argument $env)
    {
        $this->environment[] = $env;

        return $this;
    }

    public function parseEnvironmentToArgument() : void
    {
        foreach ($this->environment as $envArg) {
            $this->args->prepend(new Argument('-e', Unescaped::make((string) $envArg), ' '));
        }
    }
}
