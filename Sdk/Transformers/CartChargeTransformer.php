<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\CartCharge;
use stdClass;

/***
 * Class CartChargeTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CartChargeTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     * @return CartCharge
     */
    public function transform(stdClass $obj)
    {
        $charge = new CartCharge(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $charge->setTotal($obj->Total);
        $charge->setPrice($obj->Price);
        $charge->setCustoms($obj->Customs);
        $charge->setShipping($obj->Shipping);
        $charge->setInsurance($obj->Insurance);
        $charge->setTaxes($obj->Taxes);
        $charge->setDuties($obj->Duties);
        $charge->setHandling($obj->Handling);
        $charge->setClearence($obj->Clearence);
        $charge->setOthers($obj->Others);
        $charge->setAdjustment($obj->Adjustment);

        return $charge;
    }
}
