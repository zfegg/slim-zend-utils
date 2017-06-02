<?php


namespace ZfeggTest\ZendUtils;


use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ServiceManagerTest extends TestCase
{
    public function testOffsetAndMagicMethods()
    {
        $testObj = new \stdClass();
        $container = new \Zfegg\ZendUtils\ServiceManager();
        $container['testInvokable'] = 'stdClass';
        $container['testFactory'] = function (ContainerInterface $c) use ($testObj) {
            return $testObj;
        };
        $container['testService'] = $testObj;

        $this->assertInstanceOf(\stdClass::class, $container['testInvokable']);
        $this->assertEquals($testObj, $container['testFactory']);
        $this->assertEquals($testObj, $container['testService']);
        $this->assertEquals($testObj, $container->testFactory);
        $this->assertEquals($testObj, $container->testService);
        $this->assertTrue(isset($container['testFactory']));
        $this->assertTrue(isset($container->testFactory));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testOffsetUnset()
    {
        $container = new \Zfegg\ZendUtils\ServiceManager();
        $container['test'] = [];

        unset($container['test']);
    }
}