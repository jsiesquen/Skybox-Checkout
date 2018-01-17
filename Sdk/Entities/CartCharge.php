<?php

namespace Skybox\Checkout\Sdk\Entities;

/**
 * Class CartCharge
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method CartCharge setTaxes(float $value)
 * @method CartCharge setDuties(float $value)
 * @method CartCharge setHandling(float $value)
 * @method CartCharge setClearence(float $value)
 * @method CartCharge setOthers(float $value)
 * @method float getTaxes()
 * @method float getDuties()
 * @method float getHandling()
 * @method float getClearence()
 * @method float getOthers()
 */

class CartCharge extends Charge
{

    /**
     * @var float
     */
    public $taxes = 0.0;

    /**
     * @var float
     */
    public $duties = 0.0;

    /**
     * @var float
     */
    public $handling = 0.0;

    /**
     * @var float
     */
    public $clearence = 0.0;

    /**
     * @var float
     */
    public $others = 0.0;
}
