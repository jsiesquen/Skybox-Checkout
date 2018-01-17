<?php

namespace Skybox\Checkout\Sdk\Entities;

use Exception;

/***
 * Class AbstractEntity
 * @package Skybox\Checkout\Sdk\Entities
 */
abstract class AbstractEntity
{

    /**
     * Set/Get attribute wrapper
     *
     * @param string $method
     * @param array $args
     * @return $this
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}($args);
        }
        switch (substr($method, 0, 3)) {
            case 'set':
                $value = str_replace('set', '', $method);
                $property = strtolower($value[0]) . substr($value, 1);
                if (property_exists($this, $property)) {
                    $this->{$property} = $args[0];
                    return $this;
                }
                break;
            case 'get':
                $value = str_replace('get', '', $method);
                $property = strtolower($value[0]) . substr($value, 1);
                if (property_exists($this, $property)) {
                    return $this->{$property};
                }
                break;
        }
        throw new Exception("Invalid method " . get_class($this) . "::" . $method . ")"); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    }
}
