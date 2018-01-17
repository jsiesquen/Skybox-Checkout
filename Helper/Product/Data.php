<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Helper\Product;

/**
 * Class Data
 * @package Skybox\Checkout\Helper\Product
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Skybox\Checkout\Helper\Config
     */
    private $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Skybox\Checkout\Helper\Config $configHelper
    ) {
        $this->priceHelper  = $priceHelper;
        $this->dataHelper   = $dataHelper;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * Returns the base price text according to the configured template
     *
     * @param array $dataProduct
     *
     * @return mixed
     */
    public function getBasePriceText($dataProduct)
    {
        $basePrice = null;
        try {
            $basePrice = $this->calculatePrice($dataProduct);
        } catch (\Exception $exception) {
            $this->_logger->debug('[SBC] Data:getBasePriceText (exception):' . $exception->getMessage());
        }

        return $basePrice;
    }

    /**
     * @param $dataProduct
     *
     * @return array|string
     */
    public function calculatePrice($dataProduct)
    {
        if (empty((float)$dataProduct['price']) || $dataProduct['product_type'] === 'downloadable') {
            return '-';
        }

        try {
            $product = new \Skybox\Checkout\Sdk\Entities\Product;   /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $product->setHtmlObjectId($dataProduct['id']);
            $product->setSku($dataProduct['sku']);
            $product->setName($dataProduct['title']);
            $product->setCategory($dataProduct['commodity']);
            $product->setPrice($dataProduct['price']);
            $product->setImg($dataProduct['images']);
            $product->setWeight($dataProduct['weight']);
            $product->setWeightUnit($dataProduct['weight_unit']);
            $product->setVolumetricWeight($dataProduct['volumetric_weight']);

            $store                = $this->getClient();
            $internationalProduct = $store->getCatalog()->calculate($product);
            $result               = $internationalProduct->getHtmlButton();
        } catch (\Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException $skyBoxErrorException) {
            $this->_logger->debug("[SBC] Data::calculatePrice: " . $skyBoxErrorException->getMessage());
        }

        return $result;
    }

    /**
     * @return \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public function getClient()
    {
        return $this->dataHelper->getClient();
    }

    /**
     * @return \Skybox\Checkout\Helper\Config
     */
    public function getConfig()
    {
        return $this->configHelper;
    }

    /**
     * @return \Skybox\Checkout\Helper\Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }
}
