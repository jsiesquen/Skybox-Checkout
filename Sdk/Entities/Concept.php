<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Concept
 * @package Skybox\Checkout\Sdk\Entities

 * @method Concept setId(integer $value)
 * @method Concept setName(string $value)
 * @method Concept setPrice(float $value)
 * @method Concept setPriceUsd(float $value)
 * @method Concept setCurrency(string $value)
 * @method integer getId()
 * @method string getName()
 * @method float getPrice()
 * @method float getPriceUsd()
 * @method float getCurrency()
 */

class Concept extends AbstractEntity
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float
     */
    public $price = 0.0;

    /**
     * @var float
     */
    public $priceUsd = 0.0;

    /**
     * @var float
     */
    public $currency = 0.0;
}
