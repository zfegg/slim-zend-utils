<?php
namespace Zfegg\ZendUtils\ServiceManager;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class PluginManagerConfigureDelegator implements DelegatorFactoryInterface
{
    protected $configKey;

    public function __construct($configKey)
    {
        $this->configKey = $configKey;
    }

    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        $configs = $container->get('config');
        $instance = $callback();

        if (isset($configs[$this->configKey])) {
            $instance->configure($configs[$this->configKey]);
        }

        return $instance;
    }
}
