<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Multicalculate implements ObserverInterface
{
    protected $session;

    private $logger;
    private $productBlock;
    private $configHelper;
    private $client;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * Constructor.
     *
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Skybox\Checkout\Block\Product\AfterPrice $productBlock
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Skybox\Checkout\Block\Product\AfterPrice $productBlock,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->configHelper = $configHelper;
        $this->productBlock = $productBlock;
        $this->dataHelper   = $dataHelper;
        $this->session      = $session;
        $this->logger       = $logger;
        $this->client       = $this->dataHelper->getClient();

        $this->session->setData('multicalculate_response', array());
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer) {
        if ($this->isActive()) {
            $products           = [];
            $collection         = $observer->getEvent()->getData('collection');
            $disableProducts    = ['downloadable', 'grouped', 'virtual'];

            if (is_null($collection)) {
                return false;
            }

            try {
                foreach ($collection as $product) {
                    if (in_array($product->getTypeId(), $disableProducts)) {
                        continue;
                    }

                    $dataProduct = $this->productBlock->getDataProduct($product);

                    $productItem = new \Skybox\Checkout\Sdk\Entities\Product(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                    $productItem->setHtmlObjectId($dataProduct['html_object_id']);
                    $productItem->setId($dataProduct['id']);
                    $productItem->setSku($dataProduct['sku']);
                    $productItem->setName($dataProduct['title']);
                    $productItem->setCategory($dataProduct['commodity']);
                    $productItem->setPrice($dataProduct['price']);
                    $productItem->setImg($dataProduct['images']);
                    $productItem->setWeight($dataProduct['weight']);
                    $productItem->setWeightUnit($dataProduct['weight_unit']);
                    $productItem->setVolumetricWeight($dataProduct['volumetric_weight']);
                    $products[] = $productItem;
                }

                $resultResponse = null;
                if (count($products) > 0) {
                    $resultResponse = $this->client->getCatalog()->multiCalculate($products);

                    $collection_response = $this->session->getData('multicalculate_response');

                    $this->logger->debug('[SBC] $resultResponse: ' . json_encode($resultResponse) . "\n");

                    $collection_response = array_merge($collection_response, $resultResponse);
                    $this->logger->debug('[SBC] $collection_response: ' . json_encode($collection_response) . "\n");
                    $this->session->setData('multicalculate_response', $collection_response);
                }
            } catch (\Exception $exception) {
                $this->logger->debug("[SBC] Observer\MultiCalculate::execute (exception): " . $exception->getMessage());
            } catch (\Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException $exception) {
                $this->logger->debug("[SBC] Observer\MultiCalculate::execute (skyboxerrorexception): " . $exception->getMessage());
            }
        }
    }

    /**
     * @return bool
     */
    private function isActive()
    {
        if (is_null($this->dataHelper->getClient())) {
            return false;
        }
        $allow = $this->dataHelper->allowed();
        if ($allow->isPriceEnabled()) {
            return true;
        }
        if ($this->configHelper->getEnabled() === '1') {
            return true;
        }

        return false;
    }
}
