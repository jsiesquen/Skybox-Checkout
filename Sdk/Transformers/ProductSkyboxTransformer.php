<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\Product;

/***
 * CustomerDeviceSkyboxTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class ProductSkyboxTransformer extends KTransformer
{
    /**
     * @param Product $product
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            "HtmlObjectId"      => $product->getHtmlObjectId(),
            "ProductMerchantId" => $product->getId(),
            "Sku"               => $product->getSku(),
            "Name"              => $product->getName(),
            "Category"          => $product->getCategory(),
            "Price"             => $product->getPrice(),
            "ImgUrl"            => $product->getImg(),
            "Weight"            => $product->getWeight(),
            "WeightUnit"        => $product->getWeightUnit(),
            "VolumetricWeight"  => $product->getVolumetricWeight(),
            "Quantity"          => $product->getQuantity(),
        ];
    }
}
