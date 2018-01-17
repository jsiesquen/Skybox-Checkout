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
interface StatusOrderInterface
{
    /**
     * Returns Order Status
     *
     * @api
     * @param string $orderIncrementId Order increment id.
     * @param string $status Order status.
     * @return string Successfully.
     */
    public function update(
        $orderIncrementId,
        $status
    );
}
