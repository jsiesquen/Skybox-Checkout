<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Minicart;

/**
 * CustomerDataCart
 * @package Skybox\Checkout\Plugin\Minicart
 * @see \Magento\Tax\Plugin\Checkout\CustomerData\Cart
 */
class CustomerDataCart
{
    /**
     * Constructor.
     *
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_configHelper = $configHelper;
        $this->_dataHelper   = $dataHelper;
        $this->_logger       = $logger;
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterAfterGetSectionData($subject, $result)
    {
        $subjectVar = $subject;
        if ($this->isEnable()) {
            $currency                    = $this->_dataHelper->getCurrencySymbol();
            $subtotal                    = $this->_dataHelper->getSkyboxSubtotal();
            $result['subtotal_excl_tax'] = sprintf('<span class="price">%s</span>', ($currency . $subtotal));
            $this->_logger->debug("[SBC] CustomerDataCart::afterAfterGetSectionData: SubTotal - " . (string)$subtotal);
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isEnable()
    {
        if (is_null($this->_dataHelper->getClient())) {
            return false;
        }
        if (!$this->_configHelper->getEnabled()) {
            return false;
        }
        if (!$this->_dataHelper->allowed()->isCartButtonEnabled()) {
            return false;
        }

        return true;
    }
}
