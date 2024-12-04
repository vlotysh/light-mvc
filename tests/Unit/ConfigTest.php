<?php

namespace LightMVC\Tests\Unit;

use LightMVC\Core\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        Config::set('test.key', 'value');
        Config::set('test.nested.key', 'nested value');
        Config::set('test.array', ['one' => 1, 'two' => 2]);
    }

    public function testCanGetConfigValue()
    {
        $this->assertEquals('value', Config::get('test.key'));
    }

    public function testCanGetNestedConfigValue()
    {
        $this->assertEquals('nested value', Config::get('test.nested.key'));
    }

    public function testReturnsDefaultValueWhenKeyNotExists()
    {
        $this->assertEquals('default', Config::get('test.not.exists', 'default'));
    }

    public function testCanCheckIfConfigExists()
    {
        $this->assertTrue(Config::has('test.key'));
        $this->assertFalse(Config::has('test.not.exists'));
    }

    public function testCanGetArrayValue()
    {
        $this->assertEquals(['one' => 1, 'two' => 2], Config::get('test.array'));
    }
}