<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Checkout;

// Interceptor: Magento\Checkout\Model\Checkout

use Braintree\Exception;
use Skybox\Checkout\Sdk\Exceptions\SkyboxException;
use Skybox\Checkout\Sdk\Entities\Product;
use Skybox\Checkout\Sdk\Exceptions\SkyboxNotAcceptableException;

/**
 * Class CartPlugin
 * @package Skybox\Checkout\Plugin\Checkout
 * @see \Magento\Checkout\Model\Cart
 */
class CartPlugin
{
    /**
     * Holds the registry key for the Quote Item Id
     */
    const QUOTE_ITEM_ID_KEY = 'quote_item_id';

    /**
     * Holds the registry key for the Skybox Product Id
     */
    const SKYBOX_PRODUCT_ID_KEY = 'skybox_product_id';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $session;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Registry|null
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * Checkout session object
     *
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Skybox\Checkout\Block\Product\AfterPrice
     */
    private $afterPrice;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * CartPlugin constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Backend\Model\Session\Quote $session
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Skybox\Checkout\Block\Product\AfterPrice $afterPrice
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product $product,
        \Magento\Backend\Model\Session\Quote $session,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        \Skybox\Checkout\Block\Product\AfterPrice $afterPrice,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct
    ) {
        $this->_objectManager      = $context->getObjectManager();
        $this->productRepository   = $productRepository;
        $this->product             = $product;
        $this->session             = $session;
        $this->request             = $context->getRequest();
        $this->messageManager      = $context->getMessageManager();
        $this->configHelper        = $configHelper;
        $this->dataHelper          = $dataHelper;
        $this->imageHelper         = $imageHelper;
        $this->storeManager        = $storeManager;
        $this->coreRegistry        = $registry;
        $this->checkoutSession     = $checkoutSession;
        $this->quote               = $checkoutSession->getQuote();
        $this->logger              = $logger;
        $this->afterPrice          = $afterPrice;
        $this->configurableProduct = $configurableProduct;
    }

    /**
     * Add product to Shopping Checkout (quote)
     *
     * @param $subject
     * @param \Magento\Checkout\Model\Cart $cart
     *
     * @return \Magento\Checkout\Model\Cart
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterAddProduct($subject, \Magento\Checkout\Model\Cart $cart)
    {
        $subject = $subject;
        if (!$this->isEnable()) {
            return $cart;
        }

        $storeId    = $this->session->getStore()->getId();
        $productId  = $this->request->getParam('product', 0);
        $quantity   = $this->request->getParam('qty', 1);
        $quoteItem  = null;

        try {
            $product    = $this->productRepository->getById($productId, false, $storeId);
            $item       = $cart->getQuote()->getItemByProduct($product);
            $cartQty    = $item->getQty();
            $quoteItem  = $item->getQuoteId();

            if ($product->getTypeId() === 'simple') {
                $dataProduct = $this->afterPrice->getDataProduct($item->getProduct(), null, $cartQty);
            } else {
                if ($product->getTypeId() === 'configurable') {
                    $dataProduct = $this->getProductIdFrom($product, $cartQty);
                }
            }
            $this->logger->debug("[SBC] CartPlugin::afterAddProduct: Quantity: {$quantity}; Data:" .
                                 json_encode($dataProduct));

            $this->updateQuoteItem($item, $dataProduct, $quantity);
        } catch (\Exception $exception) {
            $this->logger->debug("[SBC] CartPlugin::afterAddProduct (exception): " . $exception->getMessage());
            $this->messageManager->addErrorMessage(__('[Skybox] Failed to add the product to the cart.'));
            if ($quoteItem) {
                $cart->removeItem($quoteItem);
            }
        }
    }

    /**
     * Update Checkout Items information
     *
     * @param $subject
     * @param \Magento\Checkout\Model\Cart $cart
     *
     * @return \Magento\Checkout\Model\Cart
     */
    public function afterUpdateItems($subject, \Magento\Checkout\Model\Cart $cart)
    {
        $this->logger->debug("[SBC] CartPlugin::afterUpdateItems");
        $subjectVar = $subject;

        if (!$this->isEnable()) {
            return $cart;
        }

        $data = $this->getRequestData();

        foreach ($data as $itemId => $itemInfo) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $item = $cart->getQuote()->getItemById($itemId);

            if (!$item) {
                continue;
            }

            $skyboxProductId = (int)$item->getData('skybox_product_id');
            $client          = $this->dataHelper->getClient();

            if (!empty($itemInfo['remove']) || isset($itemInfo['qty']) && $itemInfo['qty'] == '0') {
                $this->logger->debug("[SBC] CartPlugin::afterUpdateItems: Removing SkyBox Product:{$skyboxProductId}");
                try {
                    $result = $client->getCart()->removeItem($skyboxProductId);
                    if ($result) {
                        $this->logger->debug("[SBC] CartPlugin::afterUpdateItems: Removed SkyBox Product:{$skyboxProductId}");
                    }
                } catch (\Exception $exception) {
                    $this->logger->debug("[SBC] CartPlugin::afterUpdateItems (exception): " . $exception->getMessage());
                }
                continue;
            }

            $quantity = isset($itemInfo['qty']) ? (double)$itemInfo['qty'] : false;
            if ($quantity > 0) {
                try {
                    $data = $this->afterPrice->getDataProduct($item->getProduct(), null, $quantity);
                    $result = $client->getCart()->updateItem($skyboxProductId, $quantity, $data['price']);
                    if ($result) {
                        $this->logger->debug(
                            sprintf(
                                "[SBC] CartPlugin::afterUpdateItems: Updating SkyBox Product - %s; Quantity: %s",
                                $skyboxProductId,
                                $quantity
                            )
                        );
                    }
                } catch (\Exception $exception) {
                    $this->logger->debug("[SBC] CartPlugin::afterUpdateItems (exception): " . $exception->getMessage());
                }
            }
        }

        $this->checkoutSession->setUpdateLocalStorage(1);
        $this->dataHelper->cleanAuth($client);

        return $cart;
    }

    /**
     * Remove Item from Shopping Checkout
     *
     * @param \Magento\Checkout\Model\Cart $cart
     *
     * @return \Magento\Checkout\Model\Cart
     */
    public function beforeRemoveItem(\Magento\Checkout\Model\Cart $cart)
    {
        if (!$this->isEnable()) {
            return;
        }

        $itemId = null;

        if ($this->request->has('id') || $this->request->has('item_id')) {
            $itemId = $this->request->getParam('id');
            if (is_null($itemId)) {
                $itemId = $this->request->getParam('item_id');
            }
        }

        try {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                $this->logger->debug("[SBC] CartPlugin::beforeRemoveItem: Quote Item Not Found!");

                return $cart;
            }

            $skyboxProductId    = (int)$item->getData('skybox_product_id');
            $client             = $this->dataHelper->getClient();

            if (!empty($skyboxProductId)) {
                $result = $client->getCart()->removeItem($skyboxProductId);

                if ($result) {
                    $this->logger->debug("[SBC] CartPlugin::beforeRemoveItem: Removed SkyBox Product - {$skyboxProductId}");
                }
            } else {
                $this->logger->debug("[SBC] CartPlugin::beforeRemoveItem: SkyBox Product Id don't found!");
            }

            $this->checkoutSession->setUpdateLocalStorage(1);
            $this->dataHelper->cleanAuth($client);
        } catch (\Exception $exception) {
            $this->logger->debug("[SBC] CartPlugin::beforeRemoveItem: " . $exception->getMessage());
        }
    }

    /**
     * Mark all quote items as deleted (empty shopping cart)
     *
     * @param $subject
     * @param $result
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function afterTruncate($subject, $result)
    {
        $subjectVar = $subject;
        if ($this->isEnable()) {
            try {
                $client = $this->dataHelper->getClient();
                $client->getCart()->removeAll();
                $this->checkoutSession->setUpdateLocalStorage(1);
                $this->dataHelper->cleanAuth($client);
            } catch (\Exception $exception) {
                $this->logger->debug("[SBC] CartPlugin::afterTruncate: " . $exception->getMessage());
            }
        }

        return $result;
    }

    /***
     * Before Update Item - Interceptor
     *
     * @param $subject
     * @param $itemId
     *
     */
    public function beforeUpdateItem($subject, $itemId)
    {
        $subjectVar = $subject;
        if ($this->isEnable()) {
            $item   = $this->quote->getItemById($itemId);
            $itemId = $item->getItemId();

            $this->coreRegistry->unregister(self::QUOTE_ITEM_ID_KEY);
            $this->coreRegistry->register(self::QUOTE_ITEM_ID_KEY, $itemId);

            $this->coreRegistry->unregister(self::SKYBOX_PRODUCT_ID_KEY);
            $this->coreRegistry->register(self::SKYBOX_PRODUCT_ID_KEY, $item->getData('skybox_product_id'));
        }
    }

    /**
     * Update item in Shopping Checkout (quote)
     *
     * @param Object $subject
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return \Magento\Quote\Model\Quote\Item $item
     */
    public function afterUpdateItem($subject, $item)
    {
        $this->logger->debug('[SBC] CartPlugin::afterUpdateItem');
        $subjectVar = $subject;
        if ($this->isEnable()) {
            $quantity = $this->request->getParam('qty');

            $data = $this->afterPrice->getDataProduct($item->getProduct());

            $skyboxProductId = $this->coreRegistry->registry(self::SKYBOX_PRODUCT_ID_KEY);
            $_quoteItemId    = $this->coreRegistry->registry(self::QUOTE_ITEM_ID_KEY);

            $quoteItemId = $item->getItemId(); // Current Quote
            $client      = $this->dataHelper->getClient();

            // Update Product
            if ($quoteItemId == $_quoteItemId) {
                try {
                    $result = $client->getCart()->updateItem($skyboxProductId, $quantity, $data['price']);
                } catch (\Exception $exception) {
                    $this->logger->debug('[SBC] CartPlugin::afterUpdateItem: ' . $exception->getMessage());
                }
            } else {
                $result = $client->getCart()->removeItem($skyboxProductId);
                if ($result) {
                    $this->logger->debug("[SBC] CartPlugin::afterUpdateItem: Removed SkyBox Product - {$skyboxProductId}");
                }

                try {
                    $this->updateQuoteItem($item, $data, $quantity);
                } catch (\Exception $exception) {
                    $this->messageManager->addErrorMessage(__('[Skybox] Failed to add the product to the cart.'));
                }
            }

            $this->dataHelper->cleanAuth($client);
        }

        return $item;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    private function isEnable()
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

    /**
     * Update Quote Item
     *
     * @param \Magento\Quote\Model\Quote\Item $item The Quote Item
     * @param array $dataProduct The Calculate Data
     * @param integer $quantity The Quantity
     *
     * @throws SkyboxException
     */
    private function updateQuoteItem($item, $dataProduct, $quantity)
    {
        try {
            $product     = new Product();
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $product->setHtmlObjectId($dataProduct['id']);
            $product->setId($dataProduct['id']);
            $product->setSku($dataProduct['sku']);
            $product->setName($dataProduct['title']);
            $product->setCategory($dataProduct['commodity']);
            $product->setPrice($dataProduct['price']);
            $product->setImg($dataProduct['images']);
            $product->setWeight($dataProduct['weight']);
            $product->setWeightUnit($dataProduct['weight_unit']);
            $product->setVolumetricWeight($dataProduct['volumetric_weight']);
            $product->setQuantity($quantity);

            $this->logger->debug(
                '[SBC] CartPlugin::updateQuoteItem: product: ' . json_encode($product)
            );

            $client             = $this->dataHelper->getClient();
            $productCalculate   = $client->getCart()->addItem($product);
            $guid               = $client->getCart()->getEntity()->getId();
            $skyboxProductId    = $productCalculate->getId();
            $productPrice       = $productCalculate->getInternationalCharge()->getPrice();

            $this->logger->debug(
                '[SBC] CartPlugin::updateQuoteItem: productCalculate: ' . json_encode($productCalculate)
            );

            $item->setData('skybox_product_id', $skyboxProductId);
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

            $this->logger->debug(
                "[SBC] CartPlugin::updateQuoteItem: Adding SkyBox Product:{$skyboxProductId}; Price:{$productPrice}; Quantity:{$quantity}"
            );

            $this->checkoutSession->setUpdateLocalStorage(1);
            $this->dataHelper->cleanAuth($client);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('[Skybox] Failed to add the product to the cart.'));
            $this->logger->debug("[SBC] CartPlugin::updateQuoteItem (exception):" . $exception->getMessage());
        }
    }

    /**
     * @return array|mixed|null
     */
    private function getRequestData()
    {
        $data = null;
        if ($this->request->has('cart')) {
            $data = $this->request->getParam('cart');
        } else {
            $itemId         = $this->request->getParam('item_id');
            $itemQuantity   = $this->request->getParam('item_qty');
            $data = [
                $itemId => ['qty' => $itemQuantity],
            ];
        }

        return $data;
    }

    /**
     * @param $product
     * @param int $quantity
     *
     * @return array
     */
    private function getProductIdFrom($product, $quantity = 1)
    {
        $dataProduct = [];
        $productType = $product->getTypeId();
        switch ($productType) {
            case 'configurable':
                $attributes = $this->request->getParam('super_attribute');

                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable */
                $childProduct = $this->configurableProduct->getProductByAttributes($attributes, $product);

                $productId = $childProduct->getId();
                $commodity = $childProduct->getData('skybox_category_id');

                $image  = $childProduct->getData('image');
                $price  = $childProduct->getFinalPrice($quantity);
                $sku    = $childProduct->getSku();
                $title  = $childProduct->getName();
                $weight = $childProduct->getWeight();

                $commodity = isset($commodity) ? $commodity : 0;
                $weight    = isset($weight) ? $weight : 0;

                $weightUnit = $this->configHelper->getWeightUnit();

                $store    = $this->storeManager->getStore();
                $urlMedia = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $imageUrl = $urlMedia . 'catalog/product' . $image;

                $dataProduct = [
                    "id"                => $productId,
                    "title"             => $title,
                    "price"             => $price,
                    "weight"            => $weight,
                    "weight_unit"       => $weightUnit,
                    "volumetric_weight" => 0,
                    "images"            => $imageUrl,
                    "sku"               => $sku,
                    "commodity"         => $commodity,
                    "product_type"      => $productType,
                    "variant_id"        => $productId,
                ];

                break;
        }
        return $dataProduct;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    private function isAcceptable(\Magento\Catalog\Model\Product $product) {

        $client = $this->dataHelper->getClient();
        $catalog = $client->getCatalog();
        $sdkProduct = new Product();
        $sdkProduct->setHtmlObjectId($product->getId());
        $sdkProduct->setId($product->getId());
        $sdkProduct->setSku($product->getSku());
        $sdkProduct->setName($product->getName());
        $sdkProduct->setCategory($product->getCategoryId());
        $sdkProduct->setPrice($product->getPrice());
        $sdkProduct->setImg($product->getImage());
        $sdkProduct->setWeight(1);
        $sdkProduct->setWeightUnit("LBS"); // KBS
        $sdkProduct->setVolumetricWeight(0);
        $sdkProduct->setQuantity(1);
        try {
            $catalog->calculate($sdkProduct, 1);
        } catch(\Skybox\Checkout\Sdk\Exceptions\SkyboxNotAcceptableException $exception){
            return false;
        }
        return true;
    }
}
