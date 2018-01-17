<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Cart;

/**
 * RemoveEstimateShipping
 *
 * @package Skybox\Checkout\Plugin\Checkout
 * @see \Magento\Checkout\Block\Cart\LayoutProcessor
 */
class RemoveEstimateShipping
{
    private $configHelper;
    private $dataHelper;

    /**
     * RemoveEstimateShipping constructor.
     *
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     */
    public function __construct(
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper
    ) {
        $this->configHelper = $configHelper;
        $this->dataHelper   = $dataHelper;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\LayoutProcessor $subject
     * @param \Closure $proceed
     * @param $jsLayout
     *
     * @return mixed
     */
    public function aroundProcess(\Magento\Checkout\Block\Cart\LayoutProcessor $subject, \Closure $proceed, $jsLayout)
    {
        $subjectVar = $subject;
        $jsLayout   = $proceed($jsLayout);

        if ($this->isEnable()) {
            unset($jsLayout['components']['block-summary']['children']['block-shipping']);
            unset($jsLayout['components']['block-summary']['children']['summary-block-config']);

            $config = ['componentDisabled' => false];

            $jsLayout['components']['block-summary']['children']['block-shipping']['config'] = $config;
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
        if ($this->configHelper->getEnabled()) {
            $allow = $this->dataHelper->allowed();
            if ($allow->isPriceEnabled()) {
                return true;
            }
        }

        return false;
    }
}
