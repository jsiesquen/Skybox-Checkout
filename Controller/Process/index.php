<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Controller\Process;

use Magento\Checkout\Model\Session\Proxy;
use Skybox\Checkout\Sdk\Entities\Product;

class Index extends \Magento\Framework\App\Action\Action
{
    private $resultPageFactory;
    private $dataHelper;
    private $interfaceObjectManager;
    private $urlHelper;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    private $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cartObject;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Skybox\Checkout\Block\Product\AfterPrice
     */
    private $blockPrice;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Index constructor.
     *
     * @param \Skybox\Checkout\Helper\Product\Data $dataHelper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Url $urlHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @param \Magento\Checkout\Model\Cart $cartObject
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Skybox\Checkout\Block\Product\AfterPrice $blockPrice
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Skybox\Checkout\Helper\Product\Data $dataHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Url $urlHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Magento\Checkout\Model\Cart $cartObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Skybox\Checkout\Block\Product\AfterPrice $blockPrice
    ) {
        $this->dataHelper           = $dataHelper;
        $this->urlHelper            = $urlHelper;
        $this->resultPageFactory    = $resultPageFactory;
        $this->checkoutSession      = $checkoutSession;
        $this->logger               = $logger;
        $this->cartObject           = $cartObject;
        $this->storeManager         = $storeManager;
        $this->blockPrice           = $blockPrice;
        parent::__construct($context);
    }

    public function execute()
    {
        $frontEndUrl    = $this->getFrontendUrl(null, [], true);
        $returnUrl      = $this->getRequest()->getParam('return_url', $frontEndUrl);
        $client         = $this->dataHelper->getClient();

        if (!is_null($client)) {
            try {
                $quote              = $this->cartObject->getQuote();
                $magentoCartCount   = (int)$quote->getItemsQty();
                $skyboxCartCount    = count($client->getCart()->getItems());
                $differentQty       = 0;

                $this->logger->debug("[SBC] Controller\Process\Index::execute: getItems: " . json_encode($client->getCart()->getItems()));

                $this->logger->debug("[SBC] Controller\Process\Index::execute: magentoCartCount: " .
                                     $magentoCartCount . "; skyboxCartCount: " . $skyboxCartCount);

                if ($magentoCartCount !== $skyboxCartCount) {
                    $responseRemoveAll = $client->getCart()->removeAll();
                    $this->logger->debug('[SBC] Controller\Process\Index::execute: responseRemoveAll: '. json_encode($responseRemoveAll));
                    $differentQty = 1;
                }

                $allItems = $quote->getAllItems();
                $this->logger->debug('[SBC] Controller\Process\Index::execute: allItems: '. json_encode($allItems));

                if ($differentQty === 1) {
                    $this->dataHelper->getDataHelper()->cleanAuth($client);
                    $this->createCart($allItems);
                    $this->logger->debug('[SBC] Controller\Process\Index::execute: createCart complete.');
                } else {
                    $client->getCart()->getInfo();
                    $this->dataHelper->getDataHelper()->cleanAuth($client);

                    if ($client->getEntity()->getLocationAllow()) {
                        $this->updateCart($allItems);
                        $this->logger->debug('[SBC] Controller\Process\Index::execute: updateCart complete.');
                    }
                }

                $this->cartObject->saveQuote();
                $client->getCart()->getInfo();
                $client->getCart()->getConcepts();
            } catch (\Exception $exception) {
                $this->logger->debug('[SBC] Controller\Process\Index::execute (exception): ' . $exception->getMessage());
            }
        }

        $this->checkoutSession->setUpdateLocalStorage(1);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($returnUrl);

        return $resultRedirect;
    }

    /**
     * @param $data
     *
     * @return \Skybox\Checkout\Sdk\Entities\Product
     */
    private function getSkyboxProduct($data)
    {
        $skyboxProduct = new Product(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $skyboxProduct->setHtmlObjectId($data['id']);
        $skyboxProduct->setId($data['id']);
        $skyboxProduct->setSku($data['sku']);
        $skyboxProduct->setName($data['title']);
        $skyboxProduct->setCategory($data['commodity']);
        $skyboxProduct->setPrice($data['price']);
        $skyboxProduct->setImg($data['images']);
        $skyboxProduct->setWeight($data['weight']);
        $skyboxProduct->setWeightUnit($data['weight_unit']); // KBS
        $skyboxProduct->setVolumetricWeight($data['volumetric_weight']);

        return $skyboxProduct;
    }

    /**
     * @param $allItems
     */
    private function createCart($allItems)
    {
        $client         = $this->dataHelper->getClient();
        $guid           = $client->getCart()->getEntity()->getId();
        $qtyCollection  = [];

        foreach ($allItems as $item) {

            $this->logger->debug('[SBC] Controller\Process\Index::execute: item: '. json_encode($item));

            /** @var \Magento\Quote\Model\ResourceModel\Quote\Item $item */
            $quantity           = $item->getQty();
            $productType        = $item->getProduct()->getTypeId();
            $skyboxProductId    = $item->getData('skybox_product_id')? : null;
            $quoteItemId        = $item->getId();
            $noEmpty            = $item->getItemId();

            if (!empty($noEmpty)) {
                $qtyCollection[$item->getItemId()] = $quantity;
            }

            $quoteItemData[$quoteItemId]['item']            = $item;
            $quoteItemData[$quoteItemId]['skyboxProductId'] = $skyboxProductId;

            $this->logger->debug('[SBC] Controller\Process\Index::execute: productType: '. $productType);

            if ($productType !== 'configurable') {
                $dataProduct = $this->blockPrice->getDataProduct($item->getProduct());
                $this->logger->debug('[SBC] Controller\Process\Index::execute: dataProduct: '. json_encode($dataProduct));

                if (isset($qtyCollection[$item->getParentItemId()])) {
                    $qtyCollection[$item->getParentItemId()];
                } else {
                    $qtyCollection[$item->getParentItemId()] = $quantity;
                }

                $skyboxProduct = $this->getSkyboxProduct($dataProduct);
                $skyboxProduct->setQuantity($quantity);

                $this->logger->debug('[SBC] Controller\Process\Index::execute: skyboxProduct: '. json_encode($skyboxProduct));

                $productCalculate = $client->getCart()->addItem($skyboxProduct);
                $skyboxProductId  = $productCalculate->getId();
                $parentItemId     = $item->getParentItemId();

                $this->logger->debug('[SBC] Controller\Process\Index::execute: productCalculate: '. json_encode($productCalculate));

                if ($parentItemId) {
                    $item = $quoteItemData[$parentItemId]['item'];
                }

                $this->updateQuoteItem($item, $productCalculate, $guid, $skyboxProductId);
            }
        }

        $this->dataHelper->getDataHelper()->cleanAuth($client);
    }

    /**
     * @param $allItems
     */
    private function updateCart($allItems)
    {
        $client = $this->dataHelper->getClient();
        $guid   = $client->getCart()->getEntity()->getId();

        /** @var \Magento\Quote\Model\ResourceModel\Quote\Item $item */
        $previousItem  = null;
        $quoteItemData = [];

        foreach ($allItems as $item) {

            $this->logger->debug('[SBC] Controller\Process\Index::execute: item: '. json_encode($item));

            /** @var \Magento\Quote\Model\ResourceModel\Quote\Item $item */
            $quantity           = $item->getQty();
            $productType        = $item->getProduct()->getTypeId();

            $this->logger->debug('[SBC] Controller\Process\Index::execute: (skybox_product_id): '. $item->getData('skybox_product_id'));
            $this->logger->debug('[SBC] Controller\Process\Index::execute: (getId): '. $item->getId());
            $this->logger->debug('[SBC] Controller\Process\Index::execute: (getName): '. $item->getName());

            $skyboxProductId                                = $item->getData('skybox_product_id')? : null;
            $quoteItemId                                    = $item->getId();
            $quoteItemData[$quoteItemId]['item']            = $item;
            $quoteItemData[$quoteItemId]['skyboxProductId'] = $skyboxProductId;

            $this->logger->debug('[SBC] Controller\Process\Index::execute: skyboxProductId: '. $skyboxProductId);

            $this->logger->debug('[SBC] Controller\Process\Index::execute: productType: '. $productType);

            if ($productType != 'configurable') {
                $dataProduct = $this->blockPrice->getDataProduct($item->getProduct());
                $this->logger->debug('[SBC] Controller\Process\Index::execute: dataProduct: '. json_encode($dataProduct));

                $skyboxProduct = $this->getSkyboxProduct($dataProduct);
                $skyboxProduct->setQuantity($quantity);

                $this->logger->debug('[SBC] Controller\Process\Index::execute: skyboxProduct: '. json_encode($skyboxProduct));

                //$productCalculate = $client->getCatalog()->calculate($skyboxProduct);   /*TODO: Change per multicalculate*/
                //$productCalculate = $client->getCart()->updateItem($skyboxProduct);
                //$data = $this->afterPrice->getDataProduct($item->getProduct(), null, $quantity);
                //$result = $client->getCart()->updateItem($skyboxProductId, $quantity, $data['price']);

                $this->logger->debug('[SBC] Controller\Process\Index::execute: quoteItemData: '. json_encode($quoteItemData));

                $parentItemId = $item->getParentItemId();
                if ($parentItemId)
                {
                    $skyboxProductId = $quoteItemData[$parentItemId]['skyboxProductId'];
                }

                $this->logger->debug('[SBC] Controller\Process\Index::execute: parentItemId: '. json_encode($parentItemId));

                $this->logger->debug("[SBC] Controller\\Process\\Index::execute: productId:{$skyboxProductId}; quantity:{$quantity}; price: {$dataProduct['price']}");

                $productCalculate = $client->getCart()->updateItem($skyboxProductId, $quantity, $dataProduct['price']);

                $this->logger->debug('[SBC] Controller\Process\Index::execute: productCalculate: '. json_encode($productCalculate));

                //$skyboxProductId  = $item->getData('skybox_product_id') ? $item->getData('skybox_product_id') : null;
                //$skyboxProductId  = $productCalculate->getId();

                if ($parentItemId) {
                    $item            = $quoteItemData[$parentItemId]['item'];
                }

                $this->updateQuoteItem($item, $productCalculate, $guid, $skyboxProductId);
            }
        }
    }

    private function objectManager($name)
    {
        if (empty($interfaceObjectManager)) {
            $this->interfaceObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }

        return $this->interfaceObjectManager->get($name);
    }

    private function getFrontendUrl($routePath, $routeParams = [], $base = false)
    {
        if (empty($this->urlHelper)) {
            $this->urlHelper = $this->objectManager('\Magento\Framework\Url');
        }

        return $base ? $this->urlHelper->getBaseUrl($routeParams) : $this->urlHelper->getUrl($routePath, $routeParams);
    }

    private function updateQuoteItem($item, $productCalculate, $guid, $skyboxProductId)
    {
        try {
            $productPrice = $productCalculate->getInternationalCharge()->getPrice();
            $total        = str_replace(",", "", $productCalculate->getInternationalCharge()->getTotal());

            $item->setPrice(floatval($productPrice)); // ugh!

            $item->setData('skybox_price', $productPrice);
            $item->setData('skybox_customs', $productCalculate->getInternationalCharge()->getCustoms());
            $item->setData('skybox_shipping', $productCalculate->getInternationalCharge()->getShipping());
            $item->setData('skybox_insurance', $productCalculate->getInternationalCharge()->getInsurance());
            $item->setData('skybox_total', $productCalculate->getInternationalCharge()->getTotal());
            $item->setData('skybox_base_price', $productCalculate->getInternationalCharge()->getBase());
            $item->setData('skybox_adjust_total', $productCalculate->getInternationalCharge()->getAdjustment());

            $item->setData('skybox_price_usd', $productCalculate->getDomesticCharge()->getPrice());
            $item->setData('skybox_customs_usd', $productCalculate->getDomesticCharge()->getCustoms());
            $item->setData('skybox_shipping_usd', $productCalculate->getDomesticCharge()->getShipping());
            $item->setData('skybox_insurance_usd', $productCalculate->getDomesticCharge()->getInsurance());
            $item->setData('skybox_total_usd', $productCalculate->getDomesticCharge()->getTotal());
            $item->setData('skybox_base_price_usd', $productCalculate->getDomesticCharge()->getBase());
            $item->setData('skybox_adjust_total_usd', $productCalculate->getDomesticCharge()->getAdjustment());

            $item->setData('skybox_guid', $guid);
            $item->setData('skybox_row_total', $total);
            $item->setData('skybox_product_id', $skyboxProductId);

            $this->logger->debug('[SBC] Controller\Process\Index::updateQuoteItem: item: ' .
                                 json_encode($item));
        } catch (\Exception $exception) {
            $this->logger->debug('[SBC] Controller\Process\Index::updateQuoteItem (exception): ' .
                                 $exception->getMessage());
        }
    }

    /**
     * @deprecated
     */
    private function clearCache()
    {
        $cacheTypelist     = $this->objectManager('\Magento\Framework\App\Cache\TypeListInterface');
        $cacheFrontendPool = $this->objectManager('\Magento\Framework\App\Cache\Frontend\Pool');
        $types             = [
            'layout',
            'block_html',
            'collections',
            'full_page'
        ];
        foreach ($types as $type) {
            $cacheTypelist->cleanType($type);
        }
        foreach ($cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
