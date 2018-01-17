<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper as AbstractHelperMage;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class AbstractHelper extends AbstractHelperMage
{
    public $storeManager;

    public function __construct(Context $context, StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
        $this->_logger = $context->getLogger();
    }
}
