<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model;

use \Magento\Framework\Session\SessionManager;

/**
 * Skybox Session model
 * @package Skybox\Checkout\Model
 * @deprecated
 */
class Session extends SessionManager
{
    public function foo()
    {
        return 'bar';
    }
}
