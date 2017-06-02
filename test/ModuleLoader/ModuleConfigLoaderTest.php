<?php
namespace ZfeggTest\ZendUtils\ModuleLoader;

use PHPUnit\Framework\TestCase;
use Zfegg\ZendUtils\ModuleLoader\ModuleConfigLoader;

class ModuleConfigLoaderTest extends TestCase
{
    public function testInitServiceManager()
    {
        $container = ModuleConfigLoader::initServiceManager([
            __NAMESPACE__ . '\\ModuleA',
            __NAMESPACE__ . '\\ModuleB',
        ], ['default' => 'test']);

        $configs = $container->get('config');
        $this->assertArraySubset([
            'default' => 'test',
            'moduleA' => 'test',
            'moduleB' => 'test',
        ], $configs);
    }
}
