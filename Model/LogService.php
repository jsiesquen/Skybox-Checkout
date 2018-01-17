<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Log Service
 */
class LogService extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init('Skybox\Checkout\Model\Resources\LogService');
    }
}
