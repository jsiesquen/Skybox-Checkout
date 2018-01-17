<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Token;
use stdClass;

/***
 * Class CartTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class TokenTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return Token
     */
    public function transform(stdClass $obj)
    {
        $token = new Token; /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        $token->setHash($obj->Token);

        return $token;
    }
}
