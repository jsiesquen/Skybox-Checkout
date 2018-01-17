<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\ProductCalculate;
use Skybox\Checkout\Sdk\Entities\ProductCharge;
use stdClass;

/**
 * Class ProductCalculateTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class ProductCalculateTransformer extends KTransformer
{
    /**
     * @param stdClass $obj
     *
     * @return ProductCalculate
     */
    public function transform(stdClass $obj)
    {
        $product = new ProductCalculate(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $product->setHtmlObjectId((int)$obj->HtmlObjectId);
        $product->setId((int)$obj->Product->Id);
        $product->setSku((string)$obj->Product->Sku);
        $product->setDomesticCharge($this->_getDomesticCharge($obj));
        $product->setInternationalCharge($this->_getInternationalCharge($obj));
        $product->setConcepts($this->_getConcepts($obj));
        $product->setHtmlButton($obj->TooltipButtonTemplate);

        return $product;
    }

    /**
     * @param stdClass $obj
     *
     * @return ProductCharge
     */
    private function _getDomesticCharge(stdClass $obj)
    {
        $chargeT = new ProductCalculateChargeTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge = new KManagerItem($obj->Product->Usd,$chargeT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $charge->get();
    }

    /**
     * @param stdClass $obj
     *
     * @return ProductCharge
     */
    private function _getInternationalCharge(stdClass $obj)
    {
        $chargeT = new ProductCalculateChargeTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge = new KManagerItem($obj->Product->Local, $chargeT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $charge->get();
    }

    /**
     * @param stdClass $obj
     *
     * @return array
     */
    private function _getConcepts(stdClass $obj)
    {
        $concepts   = array_merge($obj->Concepts, $obj->Adjusts);
        $collectionT = new ProductCalculateConceptsTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $collection = new KManagerCollection($concepts, $collectionT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $collection->get();
    }
}
