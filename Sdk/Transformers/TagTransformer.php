<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Tag;
use stdClass;

class TagTransformer extends KTransformer
{
    public function transform(stdClass $obj)
    {
        $tag = new Tag(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $tag->setArrival($obj->Arrival);
        $tag->setDenied($obj->Denied);
        $tag->setOrdered($obj->Ordered);
        $tag->setVerification($obj->Verification);

        return $tag;
    }
}
