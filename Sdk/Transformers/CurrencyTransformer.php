<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Currency;
use stdClass;

/***
 * Class CurrencyTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CurrencyTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Currency
     */
    public function transform(stdClass $obj)
    {
        $currency = new Currency; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $currency->setIso($obj->Currency);
        $currency->setSymbol($obj->Symbol);

        return $currency;
    }
}
