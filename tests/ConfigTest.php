<?php

class ConfigTest extends TestCase
{
    protected static $configKeys = [
        'routes'          => 'array',
        'css'             => 'array',
        'width'           => 'integer',
        'height'          => 'integer',
        'ignore'          => 'array',
        'storage'         => 'string',
        'pretend'         => 'bool',
        'blade_directive' => 'bool',
        'critical_bin'    => 'string',
        'timeout'         => 'integer',
    ];

    public function testConfigIsOk()
    {
        $path = realpath(__DIR__.'/../src/config/criticalcss.php');

        $this->assertFileExists($path);

        $value = require $path;

        $this->assertInternalType('array', $value);

        foreach (static::$configKeys as $key => $type) {
            $this->assertArrayHasKey($key, $value);

            $this->assertInternalType($type, $value[$key]);
        }
    }
}

function base_path($a = '')
{
    return $a;
}
