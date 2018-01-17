<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Product;

use Skybox\Checkout\Sdk\Entities\Product;

/**
 * Class WidgetProductsList
 * @package Skybox\Checkout\Plugin\Product
 * @see \Magento\CatalogWidget\Block\Product\ProductsList
 */
class WidgetProductsList
{
    const MULTI_CALCULATE_PRICE_REGISTRY_KEY = 'multi_calculate_key';
    const MULTI_CALCULATE_PRICE_RESULT_RESPONSE = 'multi_calculate_result';

    protected $session;

    private $logger;
    private $coreRegistry;
    private $productBlock;
    private $configHelper;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    private $countBefore = 0;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Skybox\Checkout\Block\Product\ProductsList $productBlock
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Skybox\Checkout\Block\Product\ProductsList $productBlock,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->coreRegistry = $registry;
        $this->configHelper = $configHelper;
        $this->dataHelper   = $dataHelper;
        $this->productBlock = $productBlock;
        $this->logger       = $logger;
        $this->session      = $session;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function beforeGetCacheKeyInfo($subject)
    {
        if ($this->isActive()) {
            $subject->addData([
                'cache_lifetime' => 0,
                'cache_tags'     => [
                    \Magento\Catalog\Model\Product::CACHE_TAG,
                ],
            ]);
        }
    }

    /**
     * Get key pieces for caching block content
     *
     * @param $subject
     * @param $collection
     *
     * @return array
     */
    public function afterGetCacheKeyInfo($subject, $collection)
    {
        $subjectVar = $subject;
        if ($this->isActive()) {
            $result[] = trim($this->dataHelper->getCurrencySymbol());
        }

        return $collection;
    }

    /**
     * @param $subject
     */
    public function beforeCreateCollection($subject)
    {
        $subjectVar        = $subject;
        $this->countBefore += 1;
        $this->coreRegistry->unregister(self::MULTI_CALCULATE_PRICE_REGISTRY_KEY);
        $this->coreRegistry->register(self::MULTI_CALCULATE_PRICE_REGISTRY_KEY, $this->countBefore);
    }

    /**
     * @param $subject
     * @param $collection
     *
     * @return mixed
     */
    public function afterCreateCollection($subject, $collection)
    {
        $subjectVar = $subject;
        if ($this->isActive()) {
            $multiCalculateFlag = $this->coreRegistry->registry(self::MULTI_CALCULATE_PRICE_REGISTRY_KEY);
            $listTypeProduct    = ['downloadable', 'grouped', 'virtual'];

            if ($multiCalculateFlag === 1) {
                $resultResponse = null;
                $products       = [];

                try {
                    foreach ($collection as $item) {
                        if (in_array($item->getTypeId(), $listTypeProduct)) {
                            continue;
                        }

                        $dataProduct = $this->productBlock->getDataProduct($item);

                        $product = new Product();   /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                        $product->setHtmlObjectId($dataProduct['html_object_id']);
                        $product->setId($dataProduct['id']);
                        $product->setSku($dataProduct['sku']);
                        $product->setName($dataProduct['title']);
                        $product->setCategory($dataProduct['commodity']);
                        $product->setPrice($dataProduct['price']);
                        $product->setImg($dataProduct['images']);
                        $product->setWeight($dataProduct['weight']);
                        $product->setWeightUnit($dataProduct['weight_unit']);
                        $product->setVolumetricWeight($dataProduct['volumetric_weight']);
                        $products[] = $product;
                    }

                    $store          = $this->dataHelper->getClient();
                    $resultResponse = $store->getCatalog()->multiCalculate($products);
                } catch (\Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException $exception) {
                    $resultResponse = null;
                    $this->logger->debug('[SBC] WidgetProductsList::afterCreateCollection (exception): ' . $exception->getMessage());
                }
                $this->coreRegistry->register(self::MULTI_CALCULATE_PRICE_RESULT_RESPONSE, $resultResponse);
                $this->session->setData('multicalculate_response', $resultResponse);
            }
        }

        return $collection;
    }

    /**
     * @return bool
     */
    private function isActive()
    {
        if ($this->configHelper->getEnabled() === '0') {
            return false;
        }
        if (is_null($this->dataHelper->getClient())) {
            return false;
        }

        $allow = $this->dataHelper->allowed();
        if ($allow->isPriceEnabled()) {
            return true;
        }

        return false;
    }
}
