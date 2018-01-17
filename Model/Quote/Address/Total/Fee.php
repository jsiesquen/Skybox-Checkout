<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Quote\Address\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class Fee extends AbstractTotal
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
     * @var array
     */
    private $skyboxCartDetails;

    private $registry;

    private $logger;

    /**
     * Init
     *
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Skybox\Checkout\Helper\Config $configHelper
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
        $this->quoteValidator = $quoteValidator;
        $this->checkoutSession  = $checkoutSession;
        $this->registry       = $registry;
        $this->configHelper   = $configHelper;
        $this->dataHelper     = $dataHelper;
        $this->logger        = $logger;
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

        if (!$this->isEnabled()) {
            return $this;
        }

        $shipAss = $shippingAssignment->getItems();
        if (empty($shipAss)) {
            return $this;
        }

        $address = $shippingAssignment->getShipping()->getAddress();

        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }

        $cartDetails = $this->dataHelper->getSkyboxCartDetails();

        if (!$cartDetails) {
            $this->logger->debug('[SBC] Fee::collect: Error trying get content from Checkout Details.');
            return $this;
        }

        $total->setData('skybox_subtotal', $cartDetails->getInternationalCharge()->getPrice());
        $total->setData('skybox_base_subtotal', '');
        $total->setData('skybox_grand_total', '');
        $total->setData('skybox_base_grand_total', '');

        $total->setData('skybox_customs_total', $cartDetails->getInternationalCharge()->getCustoms());
        $total->setData('skybox_customs_total_usd', $cartDetails->getDomesticCharge()->getCustoms());

        $total->setData('skybox_taxes_total', $cartDetails->getInternationalCharge()->getTaxes());
        $total->setData('skybox_taxes_total_usd', $cartDetails->getDomesticCharge()->getTaxes());

        $total->setData('skybox_handling_total', $cartDetails->getInternationalCharge()->getHandling());
        $total->setData('skybox_handling_total_usd', $cartDetails->getDomesticCharge()->getHandling());

        $total->setData('skybox_shipping_total', $cartDetails->getInternationalCharge()->getShipping());
        $total->setData('skybox_shipping_total_usd', $cartDetails->getDomesticCharge()->getShipping());

        $total->setData('skybox_insurance_total', $cartDetails->getInternationalCharge()->getInsurance());
        $total->setData('skybox_insurance_total_usd', $cartDetails->getDomesticCharge()->getInsurance());

        $total->setData('skybox_clearence_total', $cartDetails->getInternationalCharge()->getClearence());
        $total->setData('skybox_clearence_total_usd', $cartDetails->getDomesticCharge()->getClearence());

        $total->setData('skybox_duties_total', $cartDetails->getInternationalCharge()->getDuties());
        $total->setData('skybox_duties_total_usd', $cartDetails->getDomesticCharge()->getDuties());

        $total->setData('skybox_others_total', $cartDetails->getInternationalCharge()->getOthers());
        $total->setData('skybox_others_total_usd', $cartDetails->getDomesticCharge()->getOthers());

        $total->setData('skybox_adjust_total', $cartDetails->getInternationalCharge()->getAdjustment());
        $total->setData('skybox_adjust_total_usd', $cartDetails->getDomesticCharge()->getAdjustment());

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $totalVar = $total;
        if (!$this->isEnabled()) {
            return [];
        }

        $cartDetails = $this->dataHelper->getSkyboxCartDetails();

        if (!$cartDetails) {
            return [];
        }

        if (!$quote->isVirtual() && $quote->getShippingAddress()->getAddressType() == 'billing') {
            return [];
        }

        $concepts = $cartDetails->getConcepts();

        if (empty($concepts)) {
            return [];
        }

        $currency = $this->dataHelper->getCurrencySymbol();
        $totals = [];

        // SubTotal
        $subtotal = $this->dataHelper->getSkyboxSubtotal();

        if ($subtotal) {
            $totals[] = [
                'code'  => 'subtotal',
                'title' => __('Subtotal'),
                'value' => $subtotal,
            ];
        }
        $skyboxFeeCounter = 0;

        // Dynamic Concepts & RMT
        foreach ($concepts as $concept) {
            /** @var \Skybox\Checkout\Sdk\Entities\Concept $concept */

            $title = $concept->getName();
            $amount = $currency . $concept->getPrice();
            $totals[] = [
                'code' => "skybox_fee_{$skyboxFeeCounter}",
                'title' => __($title),
                'value' => $amount,
            ];

            $skyboxFeeCounter++;
        }

        return $totals;
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
        if (!$allow->isOperationCartEnabled()) {
            return false;
        }

        return true;
    }
}
