<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Block\Product;

/**
 * Class ProductsList
 * @package Skybox\Checkout\Block\Product
 */

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
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
     * @var \Skybox\Checkout\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Store\Model\Store
     */
    private $store;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

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
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * ProductsList constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Checkout\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Skybox\Checkout\Helper\Product\Data $helper,
        \Magento\Catalog\Model\Product $product,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Magento\Store\Model\Store $store,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Checkout\Model\Session $session,
        array $data = []
    ) {
        $this->coreRegistry         = $context->getRegistry();
        $this->helper               = $helper;
        $this->product              = $product;
        $this->configHelper         = $configHelper;
        $this->dataHelper           = $dataHelper;
        $this->store                = $store;
        $this->productRepository    = $productRepository;
        $this->categoryFactory      = $categoryFactory;
        $this->logger               = $context->getLogger();
        $this->imageHelper          = $context->getImageHelper();
        $this->session              = $session;

        /*$this->addData([
            'cache_lifetime' => 0,
            'cache_tags'     => [
                \Magento\Catalog\Model\Product::CACHE_TAG,
            ],
        ]);*/

        parent::__construct($context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder,
            $rule, $conditionsHelper, $data);
        $this->addData(array('cache_lifetime' => false));
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
            "images"            => $this->imageHelper->init($product, 'product_page_image_small')->getUrl(),
            "sku"               => $product->getSku(),
            "commodity"         => $commodity,
            "product_type"      => $product->getTypeId(),
            "variant_id"        => $product->getId(),
        ];

        return $data;
    }

    /**
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
     * @param $productId
     *
     * @return string
     */
    private function _generateProductId($productId)
    {
        $client      = $this->dataHelper->getClient();
        $currencyIso = $client->getCurrency()->getIso();
        $countryIso  = $client->getCountry()->getIso();
        $prefix      = strtolower($currencyIso . '-' . $countryIso . '-' . $productId);

        return $prefix;
    }
}
