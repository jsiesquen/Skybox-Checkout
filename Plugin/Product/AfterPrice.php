<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Product;

use \Magento\Catalog\Pricing\Price\ConfiguredPrice;

/**
 * Class AfterPrice
 * @package Skybox\Checkout\Plugin\Product
 * @see \Magento\Framework\Pricing\Render
 */
class AfterPrice
{
    /**
     * Holds the registry key for the product
     */
    const PRODUCT_REGISTRY_KEY = 'skybox_product_product';

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\Registry|null
     */
    private $coreRegistry = null;

    /**
     * @var \Skybox\Checkout\Block\Product\AfterPrice
     */
    private $afterPriceBlock;

    /**
     * @var array
     */
    private $afterPriceHtml = [];

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItemRepository;

    private $priceCode;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Registry $registry
     * @param \Skybox\Checkout\Block\Product\AfterPrice $afterPriceBlock
     * @param \Skybox\Checkout\Helper\Data $helperData
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $registry,
        \Skybox\Checkout\Block\Product\AfterPrice $afterPriceBlock,
        \Skybox\Checkout\Helper\Data $helperData,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
    ) {
        $this->afterPriceBlock     = $afterPriceBlock;
        $this->layout              = $layout;
        $this->coreRegistry        = $registry;
        $this->_helperData         = $helperData;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * Plugin in order to get the current product for price rendering
     *
     * @param \Magento\Framework\Pricing\Render $subject
     * @param $priceCode
     * @param \Magento\Catalog\Model\Product $saleableItem
     * @param array $arguments
     */
    public function beforeRender(
        \Magento\Framework\Pricing\Render $subject,
        $priceCode,
        \Magento\Catalog\Model\Product $saleableItem,
        array $arguments = []
    ) {
        $subjectVar   = $subject;
        $argumentsVar = $arguments;

        if (is_null($this->_helperData->getClient())) {
            return;
        }
        if (!$this->afterPriceBlock->isEnabled()) {
            return;
        }

        if (!$this->_helperData->allowed()->isPriceEnabled()) {
            return;
        }

        $this->coreRegistry->unregister(self::PRODUCT_REGISTRY_KEY);
        $this->coreRegistry->register(self::PRODUCT_REGISTRY_KEY, $saleableItem);
        $this->priceCode = $priceCode;
    }

    /**
     * Plugin for price rendering in order to display after price information
     *
     * @param \Magento\Framework\Pricing\Render $subject
     * @param $renderHtml string
     *
     * @return string
     */
    public function afterRender(\Magento\Framework\Pricing\Render $subject, $renderHtml)
    {
        $subjectVar = $subject;
        if (is_null($client = $this->_helperData->getClient())) {
            return $renderHtml;
        }
        if (!$this->afterPriceBlock->isEnabled()) {
            return $renderHtml;
        }
        if (!$this->_helperData->allowed()->isPriceEnabled()) {
            return $renderHtml;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->coreRegistry->registry(self::PRODUCT_REGISTRY_KEY);

        if ($product) {
            if (array_search($product->getTypeId(), array('downloadable', 'grouped', 'virtual')) === false) {
                if ($this->priceCode === \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE) {
                    $htmlUpdatePrice = sprintf(
                        "<div style='display: none;' data-role='priceBox' data-product-id='%s'></div>",
                        $product->getId()
                    );

                    return $htmlUpdatePrice . $this->_getAfterPriceHtml();
                } elseif ($this->priceCode === \Magento\Catalog\Pricing\Price\ConfiguredPrice::PRICE_CODE) {
                    $htmlPrice = '<div style="display:none;" ';
                    $htmlPrice .= 'class="price-box price-configured_price" ';
                    $htmlPrice .= 'data-product-id="' . $product->getId() . '"></div>';

                    return $htmlPrice . $this->_getAfterPriceHtml($this->priceCode);
                }
            }
        }

        return $renderHtml;
    }

    /**
     * Renders and caches the after price html
     *
     * @param $priceCode
     *
     * @return null|string
     */
    private function _getAfterPriceHtml($priceCode = null)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->coreRegistry->registry(self::PRODUCT_REGISTRY_KEY);
        // check if product is available
        if (!$product) {
            return 'no product';
        }

        // check if price for current product has been rendered before
        $codeReplace = '';
        if (!array_key_exists($product->getId(), $this->afterPriceHtml)) {
            $afterPriceBlock = $this->layout->createBlock(
                'Skybox\Checkout\Block\Product\AfterPrice',
                'baseprice_afterprice_' . $product->getId(),
                ['product' => $product]
            );
            if ($product->getTypeId() === 'configurable') {
                $templateFile = 'Skybox_Checkout::product/configurable/afterprice.phtml';
            } else {
                $templateFile = 'Skybox_Checkout::product/afterprice.phtml';
            }
            $afterPriceBlock->setTemplate($templateFile);
            $this->afterPriceHtml[$product->getId()] = $afterPriceBlock->toHtml();

            $result = $this->afterPriceHtml[$product->getId()];
        } else {
            $result = $this->afterPriceHtml[$product->getId()];
            if ($product->getTypeId() == 'bundle' && $priceCode == ConfiguredPrice::PRICE_CODE) {
                $codeReplace     = '_configured';
                $afterPriceBlock = $this->layout->createBlock(
                    'Skybox\Checkout\Block\Product\AfterPrice',
                    'baseprice_afterprice_configured' . $product->getId(),
                    ['product' => $product]
                );
                $afterPriceBlock->setTemplate('Skybox_Checkout::product/bundle/functionality.phtml');
                $result = $this->afterPriceHtml[$product->getId()] . $afterPriceBlock->toHtml();
            }
        }
        $result = str_replace('{SKYBOX_PRICE_CODE}', $codeReplace, $result);

        return $result;
    }

    /**
     * @param $productId
     *
     * @return bool
     */
    public function hasStock($productId)
    {
        $hastStock = true;

        try {
            $stock = $this->stockItemRepository->get($productId);

            if ((int)$stock->getIsInStock() === 0) {
                return false;
            }

            if ($stock->getIsInStock() && $stock->getQty() >= 1) {
                $hastStock = true;
                if ((int)$stock->getIsInStock() === 0) {
                    $hastStock = false;
                }
            }
        } catch (\Exception $e) {
            $hastStock = false;
        }

        return $hastStock;
    }
}
