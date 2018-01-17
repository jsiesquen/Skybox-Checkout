<?php

namespace Skybox\Checkout\Sdk\Transformers;

use stdClass;

/***
 * Class StoreTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class StoreTransformer extends KTransformer
{

    public function transform(stdClass $store)
    {
        return [
            'token' => $store->token,
        ];
    }
}
