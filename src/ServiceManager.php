<?php
namespace Zfegg\ZendUtils;

use Zend\ServiceManager\ServiceManager as ZendServiceManager;

/**
 * Class ServiceManager
 *
 * Add ArrayAccess
 *
 * @package Zfegg\ZendUtils
 */
class ServiceManager extends ZendServiceManager implements \ArrayAccess
{

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_string($value)) {
            $this->setInvokableClass($offset, $value);
        } elseif (is_callable($value)) {
            $this->setFactory($offset, $value);
        } else {
            $this->setService($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Deny unset container.');
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }
}
