<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\City;
use stdClass;

/***
 * Class CityTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CityTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     * @return City
     */
    public function transform(stdClass $obj)
    {
        $city = new City; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $city->setId($obj->CityId);
        $city->setName($obj->City);

        return $city;
    }
}
