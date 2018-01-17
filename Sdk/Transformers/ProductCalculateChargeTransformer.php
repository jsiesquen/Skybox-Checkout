<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\ProductCharge;
use stdClass;

/**
 * Class ProductCalculateChargeTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class ProductCalculateChargeTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return ProductCharge
     */
    public function transform(stdClass $obj)
    {
        $charge = new ProductCharge(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge->setPrice($obj->Price);
        $charge->setBase($obj->Base);
        $charge->setCustoms($obj->Customs);
        $charge->setShipping($obj->Shipping);
        $charge->setDiscount($obj->Discount);
        $charge->setInsurance($obj->Insurance);
        $charge->setTotal($obj->Total);
        $charge->setAdjustment($obj->Adjustment);

        return $charge;
    }
}
