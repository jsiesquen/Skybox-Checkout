<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Cart;

/**
 * Class ItemRendererPlugin
 * @package Skybox\Checkout\Plugin\Checkout
 * @see \Magento\Checkout\Block\Cart\Item\Renderer
 */
class ItemRendererPlugin
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    private $dataHelper;
    private $cartHelper;
    private $logger;

    /**
     * Constructor.
     *
     * @param \Skybox\Checkout\Helper\Product\Data $dataHelper
     * @param \Skybox\Checkout\Helper\Data $cartHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(
        \Skybox\Checkout\Helper\Product\Data $dataHelper,
        \Skybox\Checkout\Helper\Data $cartHelper,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->context    = $context;
        $this->dataHelper = $dataHelper;
        $this->cartHelper = $cartHelper;
        $this->layout     = $context->getLayout();
        $this->logger     = $context->getLogger();
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $item
     * @param $result
     *
     * @return string
     */
    public function afterGetUnitPriceHtml(\Magento\Checkout\Block\Cart\Item\Renderer $item, $result)
    {
        if (is_null($this->dataHelper->getClient())) {
            return $result;
        }
        if (!$this->dataHelper->getConfig()->getEnabled()) {
            return $result;
        }

        $allow = $this->cartHelper->allowed();
        if (!$allow->isPriceEnabled()) {
            return $result;
        }

        $quoteItem      = $item->getItem();
        $skyboxPrice    = $quoteItem->getData('skybox_price');

        $block = $this->layout->createBlock('Skybox\Checkout\Block\Cart\Item\Price\Renderer');
        $block->setCurrencySymbol($this->cartHelper->getCurrencySymbol());
        $block->setPrice($skyboxPrice);

        return $block->toHtml();
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $item
     * @param $result
     *
     * @return string
     */
    public function afterGetRowTotalHtml(\Magento\Checkout\Block\Cart\Item\Renderer $item, $result)
    {
        if (is_null($this->dataHelper->getClient())) {
            return $result;
        }
        if (!$this->dataHelper->getConfig()->getEnabled()) {
            return $result;
        }

        $allow = $this->cartHelper->allowed();
        if (!$allow->isPriceEnabled()) {
            return $result;
        }

        $quoteItem  = $item->getItem();
        $unitPrice  = str_replace(',', '', $quoteItem->getData('skybox_price'));
        $quantity   = $quoteItem->getQty();
        $price      = $unitPrice * $quantity;
        $price      = number_format((float)($price), 2, '.', ',');

        $block = $this->layout->createBlock('Skybox\Checkout\Block\Cart\Item\Price\Renderer');
        $block->setCurrencySymbol($this->cartHelper->getCurrencySymbol());
        $block->setPrice($price);

        return $block->toHtml();
    }
}
