<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Api;

/**
 * Generate Order Interface
 *
 * @package Skybox\Checkout\Api
 */
interface GenerateOrderInterface
{
    /**
     * Create a Order
     *
     * @api
     *
     * @param int $storeId
     * @param string $email
     * @param \Skybox\Checkout\Api\Data\ShippingAddressInterface $shippingAddress
     * @param \Skybox\Checkout\Api\Data\TotalShoppingCartInterface $TotalShoppingCart
     * @param \Skybox\Checkout\Api\Data\ProductInterface[] $Products
     * @param \Skybox\Checkout\Api\Data\CustomsInterface $Customs
     *
     * @return string Return the Order Id
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\AuthorizationException
     */
    public function create(
        $storeId,
        $email,
        $shippingAddress,
        \Skybox\Checkout\Api\Data\TotalShoppingCartInterface $TotalShoppingCart,
        $Products,
        $Customs
    );
}
