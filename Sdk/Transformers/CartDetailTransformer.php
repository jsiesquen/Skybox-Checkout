<?php

namespace Skybox\Checkout\Sdk\Transformers;

use stdClass;
use Skybox\Checkout\Sdk\Entities\CartCharge;
use Skybox\Checkout\Sdk\Entities\CartDetail;

/***
 * CartDetailTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CartDetailTransformer extends KTransformer
{
    /**
     * @param stdClass $obj
     * @return CartDetail
     */
    public function transform(stdClass $obj)
    {
        $detail = new CartDetail; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $detail->setDomesticCharge($this->_getDomesticCharge($obj));
        $detail->setInternationalCharge($this->_getInternationalCharge($obj));
        $detail->setConcepts($this->_getConcepts($obj));

        return $detail;
    }

    /**
     * @param stdClass $obj
     *
     * @return CartCharge
     */
    private function _getDomesticCharge(stdClass $obj)
    {
        $chargeT = new CartChargeTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge = new KManagerItem($obj->Product->Usd, $chargeT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $charge->get();
    }

    /**
     * @param stdClass $obj
     *
     * @return CartCharge
     */
    private function _getInternationalCharge(stdClass $obj)
    {
        $chargeT = new CartChargeTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge = new KManagerItem($obj->Product->Local, $chargeT);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $charge->get();
    }

    /**
     * @param stdClass $obj
     *
     * @return array
     */
    private function _getConcepts(stdClass $obj)
    {
        $items = array_merge($obj->Concepts, $obj->Adjusts);
        $concepts = [];
        if (!empty($items)) {
            $collectionT = new CartConceptsTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $collection = new KManagerCollection($items, $collectionT);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $concepts = $collection->get();
        }

        return $concepts;
    }
}
