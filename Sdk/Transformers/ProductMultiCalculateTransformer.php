<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\ProductCalculate;
use stdClass;

/**
 * Class ProductMultiCalculateTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class ProductMultiCalculateTransformer extends KTransformer
{

    /**
     * @param stdClass $obj
     *
     * @return array
     */
    public function transform(stdClass $obj)
    {
        $urls = null;
        if ($this->verifyItem($obj)) {
            $id        = $obj->HtmlObjectId;
            $urls[$id] = $obj->Url;
        }

        return $urls;
    }

    /**
     * @param stdClass $item
     *
     * @return bool
     */
    public function verifyItem(stdClass $item)
    {
        if (isset($item->HtmlObjectId) && isset($item->Url)) {
            return true;
        }

        return false;
    }
}
