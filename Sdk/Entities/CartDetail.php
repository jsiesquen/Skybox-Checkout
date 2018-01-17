<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class CartDetail
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method CartDetail setDomesticCharge(CartCharge $value)
 * @method CartDetail setInternationalCharge(CartCharge $value)
 * @method CartDetail setConcepts(array $value)
 * @method CartCharge getDomesticCharge()
 * @method CartCharge getInternationalCharge()
 * @method array getConcepts()
 */

class CartDetail extends AbstractEntity
{

    /**
     * @var CartCharge
     */
    public $domesticCharge;

    /**
     * @var CartCharge
     */
    public $internationalCharge;

    /**
     * @var array
     */
    public $concepts = [];
}
