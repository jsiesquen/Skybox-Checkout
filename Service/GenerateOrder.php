<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Service;

use Skybox\Checkout\Api\GenerateOrderInterface;
use Skybox\Checkout\Model\Order\Create;
use \Magento\Framework\App\ObjectManager;

class GenerateOrder implements GenerateOrderInterface
{
    /**
     * @var \Skybox\Checkout\Model\Order\Create
     */
    private $createOrder;

    /**
     * GenerateOrder constructor.
     *
     * @param Create $createOrder
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Skybox\Checkout\Model\Order\Create $createOrder
    ) {
        $this->objectManager = $objectManager;
        $this->createOrder = $createOrder;
    }

    /**
     * @param int $storeId
     * @param string $email
     * @param \Skybox\Checkout\Api\Data\ShippingAddressInterface $shippingAddress
     * @param \Skybox\Checkout\Api\Data\TotalShoppingCartInterface $totalShoppingCart
     * @param \Skybox\Checkout\Api\Data\ProductInterface[] $products
     * @param \Skybox\Checkout\Api\Data\CustomsInterface $customs
     *
     * @return mixed
     */
    public function create(
        $storeId,
        $email,
        $shippingAddress,
        \Skybox\Checkout\Api\Data\TotalShoppingCartInterface $totalShoppingCart,
        $products,
        $customs
    ) {
        $data = [];
        $data['store_id']               = $storeId;
        $data['email']                  = $email;
        $data['shipping_address']       = $shippingAddress->toArray();
        $data['total_shopping_cart']    = $totalShoppingCart;
        $data['products']               = $products;
        $data['customs']                = $customs;
        $orderData                      = $data;

        $objectManager = ObjectManager::getInstance();

        $result = $objectManager->get('Skybox\Checkout\Model\Order\Create');
        $result = $result->createOrder($storeId, $orderData);

        if (is_array($result)) {
            if (isset($result['order_id'])) {
                return $result['order_id'];
            }
        }

        return $result;
    }
}
