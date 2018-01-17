<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class City
 * @package Skybox\Checkout\Sdk\Entities

 * @method City setId(integer $value)
 * @method City setName(string $value)
 * @method string getId()
 * @method string getName()
 */

class City extends AbstractEntity
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
