<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Region;
use stdClass;

/***
 * Class RegionTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class RegionTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Region
     */
    public function transform(stdClass $obj)
    {
        $region = new Region; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        $region->setName($obj->Region);

        return $region;
    }
}
