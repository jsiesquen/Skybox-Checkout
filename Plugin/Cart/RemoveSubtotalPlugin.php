<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Cart;

class RemoveSubtotalPlugin
{
    private $configHelper;
    private $dataHelper;

    /**
     * RemoveSubtotalPlugin constructor.
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
        $this->layout       = $context->getLayout();
        $this->_logger      = $context->getLogger();
    }

    /**
     * @param \Magento\Checkout\Block\Cart\CartTotalsProcessor $subject
     * @param \Closure $proceed
     * @param $jsLayout
     *
     * @return mixed
     */
    public function aroundProcess(
        \Magento\Checkout\Block\Cart\CartTotalsProcessor $subject,
        \Closure $proceed,
        $jsLayout
    ) {
        $subjectVar = $subject;
        $jsLayout = $proceed($jsLayout);
        if (!$this->isEnable()) {
            unset($jsLayout['components']['block-totals']['children']['subtotal']);
            unset($jsLayout['components']['block-totals']['children']['tax']);
            unset($jsLayout['components']['block-totals']['children']['discount']);
        }

        return $jsLayout;
    }

    /**
     * @return bool
     */
    private function isEnable()
    {
        if (is_null($this->dataHelper->getClient())) {
            return false;
        }
        if (!$this->configHelper->getEnabled()) {
            return true;
        } else {
            $allow = $this->dataHelper->allowed();
            if (!$allow->showSubtotal()) {
                return false;
            }
        }

        return true;
    }
}
