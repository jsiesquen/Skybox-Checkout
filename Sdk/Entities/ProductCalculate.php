<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * ProductCalculate
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method ProductCalculate setId(integer $value)
 * @method ProductCalculate setSku(string $value)
 * @method ProductCalculate setHtmlObjectId(integer $value)
 * @method ProductCalculate setDomesticCharge(ProductCharge $value)
 * @method ProductCalculate setInternationalCharge(ProductCharge $value)
 * @method ProductCalculate setConcepts(array $value)
 * @method ProductCalculate setHtmlButton(string $value)
 * @method integer getId()
 * @method string getSku()
 * @method integer getHtmlObjectId()
 * @method ProductCharge getDomesticCharge()
 * @method ProductCharge getInternationalCharge()
 * @method array getConcepts()
 * @method array getHtmlButton()
 */

class ProductCalculate extends AbstractEntity
{

    /**
     * @var integer
     */
    public $id = 0;

    /**
     * @var string
     */
    public $sku = '';

    /**
     * @var integer
     */
    public $htmlObjectId = 0;

    /**
     * @var ProductCharge
     */
    public $domesticCharge = null;

    /**
     * @var ProductCharge
     */
    public $internationalCharge = null;

    /**
     * @var array
     */
    public $concepts = [];

    /**
     * @var string
     */
    public $htmlButton = '';
}
