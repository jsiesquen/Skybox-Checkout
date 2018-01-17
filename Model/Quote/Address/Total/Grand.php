<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Quote\Address\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class Grand extends AbstractTotal
{
    /**
     * @var \Magento\Quote\Model\QuoteValidator
     */
    private $quoteValidator = null;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Skybox\Checkout\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @param \Magento\Framework\Registry $registry
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Magento\Framework\Registry $registry,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->quoteValidator  = $quoteValidator;
        $this->checkoutSession = $checkoutSession;
        $this->_registry       = $registry;
        $this->configHelper    = $configHelper;
        $this->dataHelper      = $dataHelper;
        $this->_logger         = $logger;
    }

    /**
     * Collect totals process.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $totals     = array_sum($total->getAllTotalAmounts());
        $baseTotals = array_sum($total->getAllBaseTotalAmounts());
        $total->setGrandTotal($totals);
        $total->setBaseGrandTotal($baseTotals);

        if (!$this->isEnabled()) {
            return $this;
        }

        if (empty($shippingAssignment->getItems())) {
            return $this;
        }

        $grandTotal = $this->dataHelper->getSkyboxGrandTotal();
        $total->setData("skybox_grand_total", $grandTotal);

        return $this;
    }

    /**
     * Add grand total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $quoteVar = $quote;
        $result   = [
            'code'  => 'grand_total',
            'title' => __('Grand Total'),
            'value' => $total->getGrandTotal(),
            'area'  => 'footer',
        ];

        if (!$this->isEnabled()) {
            return $result;
        }

        $grandTotal = $this->dataHelper->getSkyboxGrandTotal();

        if (!$grandTotal) {
            $subtotal   = $this->dataHelper->getSkyboxSubtotal();
            $skyboxFee  = $this->dataHelper->getSkyboxFee();
            $grandTotal = $subtotal + $skyboxFee;
        }

        $result['value'] = $grandTotal;

        return $result;
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        if (is_null($this->dataHelper->getClient())) {
            return false;
        }
        if (!$this->configHelper->getEnabled()) {
            return false;
        }

        $allow = $this->dataHelper->allowed();
        if (!$allow->isPriceEnabled()) {
            return false;
        }

        return true;
    }
}
