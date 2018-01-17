<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Cart;

class RemoveMultipleAddressesPlugin
{
    private $configHelper;
    private $dataHelper;

    /**
     * RemoveMultipleAddressesPlugin constructor.
     *
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->context      = $context;
        $this->configHelper = $configHelper;
        $this->dataHelper   = $dataHelper;
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return string
     */
    public function afterToHtml($subject, $result)
    {
        $subjectVar = $subject;
        if ($this->isEnable()) {
            return '';
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isEnable()
    {
        if (is_null($this->dataHelper->getClient())) {
            return false;
        }
        if ($this->configHelper->getEnabled()) {
            $allow = $this->dataHelper->allowed();
            if ($allow->isPriceEnabled()) {
                return true;
            }
        }

        return false;
    }
}
