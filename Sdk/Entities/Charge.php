<?php

namespace Skybox\Checkout\Sdk\Entities;

/**
 * Class Charge
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Charge setTotal(float $value)
 * @method Charge setPrice(float $value)
 * @method Charge setCustoms(float $value)
 * @method Charge setShipping(float $value)
 * @method Charge setInsurance(float $value)
 * @method Charge setAdjustment(float $value)
 * @method float getTotal()
 * @method float getPrice()
 * @method float getCustoms()
 * @method float getShipping()
 * @method float getInsurance()
 * @method float getAdjustment()
 */

class Charge extends AbstractEntity
{
    
    /**
     * @var float
     */
    public $total = 0.0;

    /**
     * @var float
     */
    public $price = 0.0;

    /**
     * @var float
     */
    public $customs = 0.0;

    /**
     * @var float
     */
    public $shipping = 0.0;

    /**
     * @var float
     */
    public $insurance = 0.0;

    /**
     * @var float
     */
    public $adjustment = 0.0;
}
