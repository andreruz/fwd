<?php

namespace App\Builder;

class Command
{
    /** @var string $cwd */
    protected $cwd;

    /** @var Collection $args */
    protected $args;

    /** @var Command $wrapper */
    protected $wrapper;

    public function __construct(...$args)
    {
        $this->setWrapper($this->makeWrapper());

        $this->setArgs($this->makeArgs(...$args));
    }

    public static function make(...$args) : self
    {
        return new static(...$args);
    }

    public function getProgramName()
    {
        return '';
    }

    public function makeArgs(...$args) : array
    {
        return $args;
    }

    public function setArgs(array $args) : self
    {
        $this->args = collect();

        foreach ($args as $arg) {
            $this->addArgument($arg);
        }

        return $this;
    }

    public function addArgument($argn, $argv = null) : self
    {
        $arg = is_a($argn, Argument::class)
            ? $argn
            : new Argument($argn, $argv);

        $this->args->push($arg);

        return $this;
    }

    public function prependArgument(Argument $arg) : self
    {
        $this->args->prepend($arg);

        return $this;
    }

    public function getArguments() : array
    {
        return $this->args->map(function ($arg) {
            return (string) $arg;
        })->filter()->toArray();
    }

    public function setCwd(string $cwd) : self
    {
        if (! is_dir($cwd)) {
            throw new \InvalidArgumentException('cwd must be an existing directory');
        }

        $this->cwd = $cwd;

        return $this;
    }

    public function getCwd() : string
    {
        return $this->cwd ?: '';
    }

    public function makeWrapper() : ?Command
    {
        return null;
    }

    public function setWrapper(?Command $wrapper)
    {
        if ($wrapper) {
            $this->wrapper = $wrapper;
        }

        return $this;
    }

    public function getWrapper() : ?Command
    {
        return $this->wrapper;
    }

    protected function build()
    {
        $self = $this->beforeBuild(clone $this);

        if ($wrapper = $self->getWrapper()) {
            $wrapper = clone $wrapper;
            $wrapper->addArgument($self->parseToString());
            return $wrapper;
        }

        return $self;
    }

    protected function beforeBuild(Command $command) : Command
    {
        return $command;
    }

    public function __toString() : string
    {
        $built = $this->build();

        return get_class($built) === get_class($this)
            ? $built->parseToString()
            : (string) $built;
    }

    protected function parseToString() : string
    {
        return trim(vsprintf('%s %s', [
            $this->getProgramName(),
            $this->parseArgumentsToString(),
        ]));
    }

    protected function parseArgumentsToString() : string
    {
        return implode(' ', $this->getArguments());
    }
}
