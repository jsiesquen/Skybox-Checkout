<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Region
 * @package Skybox\Checkout\Sdk\Entities

 * @method Product set(string $value)
 * @method Product setName(string $value)
 * @method string getId()
 * @method string getName()
 */

class Region extends AbstractEntity
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;
}
