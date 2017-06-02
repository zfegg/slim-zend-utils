<?php

namespace Zfegg\ZendUtils\ModuleLoader;

use Psr\Container\ContainerInterface;
use Zfegg\ZendUtils\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ModuleConfigLoader
 *
 * @package Zfegg\ZendUtils\ModuleLoader
 */
class ModuleConfigLoader
{
    protected $modules = [];
    protected $defaultConfigs = [];

    protected $serviceListenerOptions = [];

    public function __construct(array $modules, $defaultConfigs = [])
    {
        $this->modules = $modules;
        $this->defaultConfigs = $defaultConfigs;
    }

    public function loadModuleConfigs()
    {
        $configs = $this->defaultConfigs;

        if (!empty($configs['service_listener_options'])) {
            foreach ($configs['service_listener_options'] as $options) {
                $this->addServiceListenerOptions($options);
            }
        }

        foreach ($this->modules as $moduleName) {
            $moduleName = $moduleName . '\\Module';
            $module = new $moduleName;

            //Merge getConfig
            if (method_exists($module, 'getConfig')) {
                $configs = ArrayUtils::merge($configs, $module->getConfig());
            }

            //Merge others
            foreach ($this->getServiceListenerOptions() as $options) {
                if (method_exists($module, $options['method'])) {
                    $serviceConfigs = [$options['config_key'] => $module->{$options['method']}()];
                    $configs = ArrayUtils::merge($configs, $serviceConfigs);
                }
            }
        }

        foreach ($this->getServiceListenerOptions() as $options) {
            if (isset($configs[$options['config_key']])) {
                $configs['service_manager']['delegators'][$options['service_manager']][] = function (
                    ContainerInterface $container,
                    $name,
                    callable $callback
                ) use (
                    $configs,
                    $options
                ) {
                    /** @var \Zend\ServiceManager\ServiceManager $instance */
                    $instance = $callback();
                    $instance->configure($configs[$options['config_key']]);

                    return $instance;
                };
            }
        }

        return $configs;
    }

    /**
     * @return array
     */
    public function getServiceListenerOptions()
    {
        return $this->serviceListenerOptions;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function addServiceListenerOptions(array $options)
    {
        if (!isset($options['service_manager']) || !isset($options['config_key']) || !isset($options['method'])) {
            throw new \InvalidArgumentException('Keys "service_manager","config_key","method"');
        }
        $this->serviceListenerOptions[] = $options;

        return $this;
    }

    /**
     * Init service manager
     *
     * @param array $modules
     * @param array $defaultConfigs
     * @return ServiceManager
     */
    public static function initServiceManager($modules = [], array $defaultConfigs = [])
    {
        $loader = new self($modules, $defaultConfigs);
        $configs = $loader->loadModuleConfigs();

        $services = new ServiceManager(isset($configs['service_manager']) ? $configs['service_manager'] : []);
        $services->setService('config', $configs);

        return $services;
    }
}
