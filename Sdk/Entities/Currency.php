<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Currency
 * @package Skybox\Checkout\Sdk\Entities

 * @method Currency setIso(string $value)
 * @method Currency setSymbol(string $value)
 * @method string getIso()
 * @method string getSymbol()
 */

class Currency extends AbstractEntity
{

    /**
     * @var string
     */
    public $iso;

    /**
     * @var string
     */
    public $symbol;
}
