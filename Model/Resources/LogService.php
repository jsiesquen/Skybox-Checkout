<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Resources;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LogService extends AbstractDb
{
    /**
     * Define main table
     */
    public function _construct()
    {
        $this->_init('skybox_log_service', 'id');
    }
}
