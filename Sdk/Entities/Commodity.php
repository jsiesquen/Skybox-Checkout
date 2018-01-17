<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Commodity
 * @package Skybox\Checkout\Sdk\Entities

 * @method Commodity setId(integer $value)
 * @method Commodity setDescription(string $value)
 * @method Commodity setHarmonyCode(string $value)
 * @method string getId()
 * @method string getDescription()
 * @method string getHarmonyCode()
 */

class Commodity extends AbstractEntity
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $harmonyCode;
}
