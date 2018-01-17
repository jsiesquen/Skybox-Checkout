<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * InternationalConcept
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method InternationalConcept setInternationalTotal(float $value)
 * @method InternationalConcept setInternationalPrice(float $value)
 * @method InternationalConcept setInternationalCustoms(float $value)
 * @method InternationalConcept setInternationalShipping(float $value)
 * @method InternationalConcept setInternationalInsurance(float $value)
 * @method InternationalConcept setInternationalTaxes(float $value)
 * @method InternationalConcept setInternationalDuties(float $value)
 * @method InternationalConcept setInternationalHandling(float $value)
 * @method InternationalConcept setInternationalClearence(float $value)
 * @method InternationalConcept setInternationalOthers(float $value)
 * @method InternationalConcept setInternationalAdjustment(float $value)
 * @method InternationalConcept setTotal(float $value)
 * @method InternationalConcept setProduct(float $value)
 * @method InternationalConcept setPrice(float $value)
 * @method InternationalConcept setCustoms(float $value)
 * @method InternationalConcept setShipping(float $value)
 * @method InternationalConcept setInsurance(float $value)
 * @method InternationalConcept setTaxes(float $value)
 * @method InternationalConcept setDuties(float $value)
 * @method InternationalConcept setHandling(float $value)
 * @method InternationalConcept setClearence(float $value)
 * @method InternationalConcept setOthers(float $value)
 * @method InternationalConcept setAdjustment(float $value)
 * @method InternationalConcept setConcepts(array $value)
 * @method float getInternationalTotal()
 * @method float getInternationalPrice()
 * @method float getInternationalCustoms()
 * @method float getInternationalShipping()
 * @method float getInternationalInsurance()
 * @method float getInternationalTaxes()
 * @method float getInternationalDuties()
 * @method float getInternationalHandling()
 * @method float getInternationalClearence()
 * @method float getInternationalOthers()
 * @method float getInternationalAdjustment()
 * @method array getConcepts()
 */

class InternationalConcept extends AbstractEntity
{
    /** @var float */
    public $internationalTotal = 0.0;

    /** @var float */
    public $internationalPrice = 0.0;

    /** @var float */
    public $internationalCustoms = 0.0;

    /** @var float */
    public $internationalShipping = 0.0;

    /** @var float */
    public $internationalInsurance = 0.0;

    /** @var float */
    public $internationalTaxes = 0.0;

    /** @var float */
    public $internationalDuties = 0.0;

    /** @var float */
    public $internationalHandling = 0.0;

    /** @var float */
    public $internationalClearence = 0.0;

    /** @var float */
    public $internationalOthers = 0.0;

    /** @var float */
    public $internationalAdjustment = 0.0;

    /** @var float */
    public $product;

    /** @var float */
    public $price;

    /** @var float */
    public $customs;

    /** @var float */
    public $shipping;

    /** @var float */
    public $insurance;

    /** @var float */
    public $taxes;

    /** @var float */
    public $duties;

    /** @var float */
    public $handling;

    /** @var float */
    public $clearence;

    /** @var float */
    public $others;

    /** @var float */
    public $adjustment;

    /** @var array $concepts */
    public $concepts = [];
}
