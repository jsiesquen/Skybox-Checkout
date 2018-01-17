<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Model\Data;

use Skybox\Checkout\Api\Data\ProductInterface;

class Product implements ProductInterface
{
    public $productId;
    public $quantity;
    public $productPriceUSD;
    public $productPriceCustomer;

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getProductPriceUSD()
    {
        return $this->productPriceUSD;
    }

    /**
     * @param mixed $productPriceUSD
     */
    public function setProductPriceUSD($productPriceUSD)
    {
        $this->productPriceUSD = $productPriceUSD;
    }

    /**
     * @return mixed
     */
    public function getProductPriceCustomer()
    {
        return $this->productPriceCustomer;
    }

    /**
     * @param mixed $productPriceCustomer
     */
    public function setProductPriceCustomer($productPriceCustomer)
    {
        $this->productPriceCustomer = $productPriceCustomer;
    }
}
