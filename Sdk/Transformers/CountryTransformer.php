<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\City;
use Skybox\Checkout\Sdk\Entities\Country;
use Skybox\Checkout\Sdk\Entities\Region;
use stdClass;

/***
 * Class CountryTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CountryTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Country
     */
    public function transform(stdClass $obj)
    {
        $country = new Country; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $country->setIso($obj->Iso);
        $country->setName($obj->Name);
        $country->setRegion($this->_getRegion($obj));
        $country->setCity($this->_getCity($obj));
        $country->setFlag($obj->Flag);

        return $country;
    }

    /**
     * @param stdClass $obj
     *
     * @return Region
     */
    private function _getRegion(stdClass $obj)
    {
        $regionT = new RegionTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $region = new KManagerItem($obj, $regionT);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $region->get();
    }

    /**
     * @param stdClass $obj
     *
     * @return City
     */
    private function _getCity(stdClass $obj)
    {
        $cityT = new CityTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $city = new KManagerItem($obj, $cityT);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        return $city->get();
    }
}
