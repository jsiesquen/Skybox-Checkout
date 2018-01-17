<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Block\Product;

//use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class AfterPrice
 * @package Skybox\Checkout\Block\Product
 */

class AfterPrice extends \Magento\Framework\View\Element\Template  // implements IdentityInterface
{
    const MULTI_CALCULATE_PRICE_REGISTRY_KEY    = 'multi_calculate_key';
    const MULTI_CALCULATE_PRICE_RESULT_RESPONSE = 'multi_calculate_result';

    /**
     * @var \Skybox\Checkout\Helper\Product\Data
     */
    private $helper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var string
     */
    private $configurablePricesJson;

    /**
     * @var \Skybox\Checkout\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Skybox\Checkout\Sdk\Entities\Currency
     * @var \Skybox\Checkout\Sdk\Entities\Country
    */
    protected $client;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Checkout\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Skybox\Checkout\Helper\Product\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Magento\Store\Model\Store $store,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Checkout\Model\Session $session,
        array $data = []
    ) {
        $this->coreRegistry         = $registry;
        $this->helper               = $helper;
        $this->product              = $product;
        $this->configHelper         = $configHelper;
        $this->dataHelper           = $dataHelper;
        $this->stockItemRepository  = $stockItemRepository;
        $this->store                = $store;
        $this->productRepository    = $productRepository;
        $this->categoryFactory      = $categoryFactory;
        $this->logger               = $context->getLogger();
        $this->imageHelper          = $imageHelper;
        $this->session              = $session;

        $this->client               = $this->dataHelper->getClient();

        parent::__construct($context, $data);
        $this->addData(array('cache_lifetime' => false));
    }

    /**
     * Returns the configuration if module is enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->configHelper->getEnabled();
    }

    /**
     * Returns the configuration if Button is visible
     *
     * @return mixed
     */
    public function isVisible()
    {
        return $this->dataHelper->isVisible();
    }

    /**
     * Returns method helper data
     *
     * @return mixed
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * Retrieve current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Returns the price information
     *
     * @param null $dataProduct
     *
     * @return mixed|string
     */
    public function getBasePrice($dataProduct = null)
    {
        $template       = '';
        $result_format  = '<div style="margin-bottom: 10px;" class="skybox-price-set" id="product-%s" data-product-id="%s" data-url-template="%s"></div>';
        $response       = $this->session->getData('multicalculate_response');
        $dataProduct    = !is_null($dataProduct) ? $dataProduct : $this->getDataProduct();
        $productId      = $dataProduct['html_object_id'];

        try {
            if (!is_null($response) && isset($response[$productId])) {
                $template = $response[$productId];
            } else {
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

                $resultResponse = null;
                if (count($products) > 0) {
                    $resultResponse = $this->client->getCatalog()->multiCalculate($products);
                    $template = $resultResponse[$productId];

                    $resultResponse = is_null($resultResponse) ? : array();

                    $this->logger->debug('[SBC] AfterPrice::getBasePrice: template: ' . $template);

                    $collection_response = is_null($this->session->getData('multicalculate_response')) ? : array();
                    $collection_response = array_merge($collection_response, $resultResponse);
                    $this->session->setData('multicalculate_response', $collection_response);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->debug("[SBC] AfterPrice::getBasePrice (exception): " . $exception->getMessage());
        } catch (\Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException $exception) {
            $this->logger->debug("[SBC] AfterPrice::getBasePrice (skyboxerrorexception): " . $exception->getMessage());
        }

        return sprintf(
            $result_format,
            $productId,
            $productId,
            $template
        );
    }

    /**
     * @param null $product
     * @param null $price
     * @param int $quantity
     *
     * @return array
     */
    public function getDataProduct($product = null, $price = null, $quantity = 1)
    {
        $product   = !is_null($product) ? $product : $this->getProduct();
        $commodity = $this->getCommodity($product);
        if ($commodity === 0) {
            $commodity = 77;
        }
        $data = [
            "html_object_id"    => $this->_generateProductId($product->getId()),
            "id"                => $product->getId(),
            "title"             => $this->getNameProduct($product),
            "price"             => $this->getPriceProduct($product, $price, $quantity),
            "weight"            => $this->getWeight($product),
            "weight_unit"       => $this->configHelper->getWeightUnit(),
            'volumetric_weight' => 0,
            "images"            => $this->getImage($product),
            "sku"               => $product->getSku(),
            "commodity"         => $commodity,
            "product_type"      => $product->getTypeId(),
            "variant_id"        => $product->getId(),
        ];

        return $data;
    }

    public function getImage($product)
    {
        return $this->imageHelper->init($product, 'product_page_image_small')->getUrl();
    }

    /**
     * Get Commodity
     *
     * @param $product
     *
     * @return int
     */
    public function getCommodity($product)
    {
        $commodity = $product->getSkyboxCategoryId();
        if (empty($commodity)) {
            $prod      = $this->productRepository->getById($product->getId());
            $commodity = $prod->getData('skybox_category_id');
        }

        if (!$commodity) {
            $commodity = $this->getCommodityFromCategory($product);
        }

        return !empty($commodity) ? $commodity : 0;
    }

    /**
     * Return Commodity from Category
     *
     * @param $product
     *
     * @return int
     */
    public function getCommodityFromCategory($product)
    {
        $categories = $product->getCategoryIds();
        $commodity  = 0;

        if (!empty($categories)) {
            $firstCategoryId = $categories[0];
            $_category       = $this->categoryFactory->create()->load($firstCategoryId);
            $commodity       = $_category->getSkyboxCategoryId();
        }

        if (!$commodity) {
            $rootCategoryId = $this->store->getRootCategoryId();
            $_category      = $this->categoryFactory->create()->load($rootCategoryId);
            $commodity      = $_category->getSkyboxCategoryId();
        }

        return $commodity;
    }

    /**
     * Return Commodity from Category
     *
     * @param $product
     *
     * @return int
     */
    public function getCommodityFromCategoryArchive($product)
    {
        $categoryId = $product->getCategoryId();
        $category   = $this->getCategoryById($categoryId);
        $commodity  = $category->getData('skybox_category_id');

        $this->logger->debug("[SBC] AfterPrice::getCommodityFromCategoryArchive: CategoryId - {$category->getId()}; Commodity: {$commodity}");

        if (!$commodity) { // from Parent Category
            $category = $category->getParentCategory();
            $commodity = $category->getData('skybox_category_id');
            $this->logger->debug("[SBC] AfterPrice::getCommodityFromCategoryArchive: ParentCategory - {$category->getId()}; Commodity: {$commodity}");
        }

        if (!$commodity) { // from Root Category
            $rootCategoryId = $this->getStoreManager()->getStore(1)->getRootCategoryId();

            $this->logger->debug("[SBC] AfterPrice::getCommodityFromCategoryArchive: RootCategoryId - " . $rootCategoryId);
            $category = $this->getCategoryById($rootCategoryId);
            $category = $category->getParentCategory();
            $commodity = $category->getData('skybox_category_id');
            $this->logger->debug("[SBC] AfterPrice::getCommodityFromCategoryArchive: RootCategory - {$category->getId()}; Commodity: {$commodity}");
        }

        return $commodity;
    }

    /**
     * @param $product
     * @param null $default_weight
     *
     * @return float|int|null
     */
    public function getWeight($product, $default_weight = null)
    {
        if (!is_null($default_weight)) {
            return $default_weight;
        }

        $weight = $product->getWeight();
        switch ($product->getTypeId()) {
            case 'configurable':
                if (is_null($weight)) {
                    $configurableOptions = $product->getTypeInstance()->getUsedProducts($product);
                    if (!empty($configurableOptions)) {
                        foreach ($configurableOptions as $key => $child) {
                            $weight = $child->getWeight();
                            break;
                        }
                    }
                }
                break;
            case 'simple':
                if (is_null($weight)) {
                    $productModel = $this->product->load($product->getId());
                    $weight       = $productModel->getWeight();
                }
                break;
            default:
                $weight = $product->getWeight();
                break;
        }

        if (is_null($weight)) {
            return 0;
        }

        return $weight;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $price
     * @param int $quantity
     *
     * @return mixed|null|string
     */
    public function getPriceProduct(\Magento\Catalog\Model\Product $product, $price = null, $quantity = 1)
    {
        if (!is_null($price)) {
            return $price;
        }

        switch ($product->getTypeId()) {
            case 'configurable':
                $finalPrice = $product->getFinalPrice($quantity);
                $tierPrice  = $this->getTierPriceProduct($product);
                $price      = $finalPrice > $tierPrice ? $tierPrice : $finalPrice;
                break;
            case 'simple':
                $finalPrice = $product->getFinalPrice($quantity);
                $tierPrice  = $this->getTierPriceProduct($product);
                $price      = $finalPrice > $tierPrice ? $tierPrice : $finalPrice;
                break;
            case 'grouped':
            case 'bundle':
                $bundleObj = $product->getPriceInfo()->getPrice('final_price');
                $price     = (string)$bundleObj->getMinimalPrice();
                break;
            case 'downloadable':
                $link = $product->getLinkId();
                if (empty($link)) {
                    $finalPrice = $product->getFinalPrice($quantity);
                    $tierPrice  = $this->getTierPriceProduct($product);
                    $price      = $finalPrice > $tierPrice ? $tierPrice : $finalPrice;
                } else {
                    $finalPrice = $product->getFinalPrice($quantity);
                    $price      = $finalPrice;
                }
                break;
            default:
                $finalPrice = $product->getFinalPrice($quantity);
                $tierPrice  = $this->getTierPriceProduct($product);
                $price      = $finalPrice > $tierPrice ? $tierPrice : $finalPrice;
                break;
        }

        return $price;
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    public function getNameProduct($product)
    {
        switch ($product->getTypeId()) {
            case 'downloadable':
                $name = $product->getName();
                $link = $product->getLinkId();
                if (!empty($link)) {
                    $name = $product->getTitle();
                }
                break;
            default:
                $name = $product->getName();
                break;
        }

        return $name;
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    public function getTierPriceProduct($product)
    {
        $tierProduct = $product->getTierPrice();
        $price       = $product->getFinalPrice();
        if (count($tierProduct) > 1 || !empty($tierProduct)) {
            $prod        = $this->productRepository->getById($product->getId());
            $tierProduct = $prod->getTierPrice();
            foreach ($tierProduct as $item) {
                if ($item['price_qty'] == 1) {
                    $price = $item['price'];
                    break;
                }
            }
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function hasStock()
    {
        $product   = $this->getProduct();
        $stock     = $this->stockItemRepository->get($product->getId());
        $hastStock = false;
        if ($stock->getIsInStock() && $stock->getQty() >= 1) {
            $hastStock = true;
        }

        return $hastStock;
    }

    /**
     * @return mixed
     */
    public function currentProductViewDetail()
    {
        $currentProduct = $this->coreRegistry->registry('current_product');

        return $currentProduct;
    }

    /**
     * @param string $productId
     *
     * @return string
     */
    private function _generateProductId($productId = '')
    {
        $currencyIso = $this->client->getCurrency()->getIso();
        $countryIso  = $this->client->getCountry()->getIso();
        $prefix      = strtolower($currencyIso . "-" . $countryIso . '-' . $productId);

        return $prefix;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    /*public function getIdentities() {
        $this->_logger->debug("getIdentities");
        var_dump("getIdentities()");
        return $this->getProduct()->getIdentities();
    }*/
    /*public function getIdentities()
    {
        $identities = [];

        if (is_array($this->getItems()) || is_object($this->getItems()))
        {
            foreach ($this->getItems() as $item)
            {
                $identities = array_merge($identities, $item->getIdentities());
            }
        }

        return $identities;
    }*/

    /**
     * Get block cache life time
     *
     * @return int
     */
    protected function getCacheLifetime()
    {
        return 0;
    }
}
