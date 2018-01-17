<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Minicart;

/**
 * Class QuoteItemPrice
 * @package Skybox\Checkout\Plugin\Minicart
 * @see \Magento\Tax\Block\Item\Price\Renderer
 */
class QuoteItemPrice
{
    const SKYBOX_QUOTE_ITEM_PRICE = 'skybox_quote_item_price';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    private $configHelper;
    private $dataHelper;
    private $logger;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->registry     = $registry;
        $this->configHelper = $configHelper;
        $this->dataHelper   = $dataHelper;
        $this->logger       = $logger;
    }

    public function beforeFormatPrice($subject, $price)
    {
        $subjectVar = $subject;
        if ($this->isEnabled()) {
            $this->registry->unregister(self::SKYBOX_QUOTE_ITEM_PRICE);
            $this->registry->register(self::SKYBOX_QUOTE_ITEM_PRICE, $price);
        }
    }

    /**
     * Format price
     *
     * @param float $price
     *
     * @return string
     */
    public function afterFormatPrice($subject, $price)
    {
        $subjectVar = $subject;
        if ($this->isEnabled()) {
            $currency    = $this->dataHelper->getCurrencySymbol();
            $skyboxPrice = $this->registry->registry(self::SKYBOX_QUOTE_ITEM_PRICE);
            $skyboxPrice = $currency . $skyboxPrice;
            $price       = sprintf('<span class="price">%s</span>', $skyboxPrice);
        }

        return $price;
    }

    /**
     * @param $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetItemDisplayPriceExclTax($subject, $result)
    {
        if ($this->isEnabled()) {
            $quoteItem = $subject->getItem();
            $result    = $quoteItem->getData('skybox_price');
        }

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
        if (!$this->dataHelper->allowed()->isCartButtonEnabled()) {
            return false;
        }

        return true;
    }
}
