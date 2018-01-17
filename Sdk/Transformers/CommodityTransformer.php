<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Commodity;
use stdClass;

/***
 * Class TemplateTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CommodityTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Commodity
     */
    public function transform(stdClass $obj)
    {
        $commodity = new Commodity; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $commodity->setId((int)($obj->Id));
        $commodity->setDescription((string)($obj->Description));
        $commodity->setHarmonyCode((string)($obj->HarmonyCode));

        return $commodity;
    }
}
