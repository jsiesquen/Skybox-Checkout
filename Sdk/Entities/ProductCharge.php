<?php

namespace Skybox\Checkout\Sdk\Entities;

/**
 * Class ProductCharge
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method ProductCharge setBase(float $value)
 * @method ProductCharge setDiscount(float $value)
 * @method float getBase()
 * @method float getDiscount()
 */

class ProductCharge extends Charge
{

    /**
     * @var float
     */
    public $base = 0.0;

    /**
     * @var float
     */
    public $discount = 0.0;
}
