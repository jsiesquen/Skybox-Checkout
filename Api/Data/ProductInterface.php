<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Api\Data;

/**
 * Product Interface
 * @package Skybox\Checkout\Api\Data
 */
interface ProductInterface
{
    /**
     * @return mixed
     */
    public function getProductId();

    /**
     * @param mixed $productId
     */
    public function setProductId($productId);

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return mixed
     */
    public function getProductPriceUSD();

    /**
     * @param mixed $productPriceUSD
     */
    public function setProductPriceUSD($productPriceUSD);

    /**
     * @return mixed
     */
    public function getProductPriceCustomer();

    /**
     * @param mixed $productPriceCustomer
     */
    public function setProductPriceCustomer($productPriceCustomer);
}
