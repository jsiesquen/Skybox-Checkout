<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Cart;
use Skybox\Checkout\Sdk\Entities\Currency;
use stdClass;

/***
 * Class CartTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CartTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     * @return Cart
     */
    public function transform(stdClass $obj)
    {
        $cart = new Cart; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $cart->setId($obj->Id);
        $cart->setTotalItems($obj->Count);
        $cart->setDataUrl($obj->DataUrl);
        $cart->setItems($obj->Items);

        return $cart;
    }
}
