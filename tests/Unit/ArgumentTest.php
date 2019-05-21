<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Builder\Argument;
use App\Builder\Command;
use App\Builder\Unescaped;

class ArgumentTest extends TestCase
{
    public function testArgumentOnlyName()
    {
        $arg = new Argument('foo');

        $this->assertEquals($arg->__toString(), '\'foo\'');
    }

    public function testArgumentWithNameAndValue()
    {
        $arg = new Argument('foo', 'bar');

        $this->assertEquals($arg->__toString(), 'foo=\'bar\'');
    }

    public function testArgumentWithNameAndValueNoSeparator()
    {
        $arg = new Argument('foo', 'bar', ' ');

        $this->assertEquals($arg->__toString(), 'foo \'bar\'');
    }

    public function testArgumentWithCommand()
    {
        $arg = new Argument(new Command('foo'));

        $this->assertEquals($arg->__toString(), 'foo');
    }

    public function testArgumentWithCommandWithArgument()
    {
        $arg = new Argument(new Command('foo', new Argument('--bar')));

        $this->assertEquals($arg->__toString(), 'foo --bar');
    }

    public function testArgumentWithValueAsArgument()
    {
        $arg = new Argument('--env', new Argument('FOO', 'bar'));

        $this->assertEquals($arg->__toString(), '--env=\'FOO=\'\\\'\'bar\'\\\'\'\'');
    }

    public function testArgumentWithValueAsUnscaped()
    {
        $arg = new Argument('--foo', Unescaped::make('bar'), ' ');

        $this->assertEquals($arg->__toString(), '--foo bar');
    }
}
