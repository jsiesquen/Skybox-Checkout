<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Attribute
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Attribute setName(string $value)
 * @method Attribute setValue(string $value)
 * @method string getName()
 * @method string getValue()
 */

class Attribute extends AbstractEntity
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;
}
