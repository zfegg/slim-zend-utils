<?php

namespace Zfegg\ZendUtils\ServiceManager;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class InjectContainerFactory
 *
 * @package Zfegg\ZendUtils\ServiceManager
 */
class InjectContainerFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }
}
