<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Concept;
use stdClass;

/***
 * CartConceptsTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CartConceptsTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Concept
     */
    public function transform(stdClass $obj)
    {
        $concept = null;
        if ((int)$obj->Visible) {
            $concept = new Concept; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $concept->setId($obj->Id);
            $concept->setName($obj->Name);
            $concept->setPrice($obj->Price);
            $concept->setPriceUsd($obj->PriceUsd);
            $concept->setCurrency($obj->Currency);
        }

        return $concept;
    }
}
