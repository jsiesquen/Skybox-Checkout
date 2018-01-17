<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Minicart;

/**
 * DataHelper Class
 * @package Skybox\Checkout\Plugin\Minicart
 * @see \Magento\Checkout\Helper\Data
 */
class DataHelper
{
    /**
     * DataHelper constructor.
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
     * Format price
     *
     * @param $subject
     * @param $price
     *
     * @return string
     */
    public function afterFormatPrice($subject, $price)
    {
        $subjectVar = $subject;
        if ($this->isEnable()) {
            $currency = $this->_dataHelper->getCurrencySymbol();
            $currency .= ' # ';
            $price = str_replace("$", $currency, $price);
        }

        return $price;
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
