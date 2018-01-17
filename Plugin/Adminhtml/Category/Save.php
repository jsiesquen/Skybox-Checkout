<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Adminhtml\Category;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Save
 * @package Skybox\Checkout\Plugin\Adminhtml\Category
 * @see \Magento\Catalog\Controller\Adminhtml\Category\Save
 */
class Save
{
    const CATEGORY_REGISTRY_KEY = 'skybox_admin_category_id';

    private $dataHelper;
    private $categoryFactory;
    private $productCollectionFactory;
    private $product;
    private $request;
    /** @var  \Magento\Catalog\Model\Category */
    private $category;
    private $messageManager;
    private $coreRegistry = null;
    private $stockStateInterface;
    private $stockRegistry;
    private $productFactory;
    private $resourceConnection = null;
    private $helperConfig;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultRedirectFactory    = $context->getResultRedirectFactory();
        $this->response                 = $response;
        $this->helperConfig             = $configHelper;
        $this->dataHelper               = $dataHelper;
        $this->categoryFactory          = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->product                  = $product;
        $this->request                  = $context->getRequest();
        $this->_logger                  = $logger;
        $this->messageManager           = $context->getMessageManager();
        $this->coreRegistry             = $registry;
        $this->stockStateInterface      = $stockStateInterface;
        $this->stockRegistry            = $stockRegistry;
        $this->productFactory           = $productFactory;
        $this->resourceConnection       = $resourceConnection;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Category\Save $subject
     */
    public function beforeExecute(\Magento\Catalog\Controller\Adminhtml\Category\Save $subject)
    {
        $subjectVar       = $subject;
        $categoryPostData = $this->request->getPostValue();

        if (isset($categoryPostData['entity_id']) & !empty($categoryPostData['entity_id'])) {
            $categoryId = $categoryPostData['entity_id'];
        } else {
            $categoryId = $categoryPostData['id'];
        }

        $cat = $this->getCategory($categoryId);
        $this->coreRegistry->unregister(self::CATEGORY_REGISTRY_KEY);
        $this->coreRegistry->register(self::CATEGORY_REGISTRY_KEY, $cat->getSkyboxCategoryId());
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Category\Save $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Category\Save $subject, $result)
    {
        $subjectVar          = $subject;
        $skyboxCategoryIdOld = $this->coreRegistry->registry(self::CATEGORY_REGISTRY_KEY);
        $categoryPostData    = $this->request->getPostValue();

        if (isset($categoryPostData['skybox_category_id'])) {
            $skyboxCategoryId = $categoryPostData['skybox_category_id'];
        } else {
            $categoryPostData['general']['skybox_category_id'];
        }

        $itemNoRepeat    = isset($categoryPostData['item_no_repeat']) ? $categoryPostData['item_no_repeat'] : 0;
        $optionRecursive = isset($categoryPostData['save_recursive']) ? $categoryPostData['save_recursive'] : 0;

        $skyboxCategoryOptions = [$itemNoRepeat, $optionRecursive];

        if (($skyboxCategoryIdOld !== $skyboxCategoryId)
            || (in_array(1, $skyboxCategoryOptions))
        ) {
            $categoryId = $categoryPostData['entity_id'];
            $storeId    = $this->helperConfig->getStoreId();

            $skyboxCommodityAttributeId = $this->getSkyboxCommodityAttributeId();

            if (empty($skyboxCommodityAttributeId)) {
                return $result;
            }

            $categoryIds = $categoryId;
            $noRepeat    = (bool)$itemNoRepeat;

            $resource   = $this->getResourceConnection();
            $connection = $resource->getConnection();

            $rowsUpdateCategoriesSql        = 0;
            $sqlUpdateCategoriesCommodity   = null;

            if ($optionRecursive === 1) {
                $categoryIds = $this->getAllChildren(false, $categoryId);
                $attributeId = $this->getSkyboxCommodityAttributeId(3);

                $rowsUpdateCategoriesSql = $this->updateCategoriesSql(
                    $categoryIds,
                    $storeId,
                    $attributeId,
                    $skyboxCategoryId,
                    $noRepeat
                );
            }

            $productByCategory = $this->listProductByCategory($categoryIds);
            $productsUpdate    = $this->listProductCommoditySkyboxByCategoryId($skyboxCommodityAttributeId);
            $productIdsUpdate  = $this->getProductIdsUpdate($productsUpdate);

            $rowsInsertProductsCommodity = $this->insertProductSql(
                $productByCategory,
                $productIdsUpdate,
                $storeId,
                $skyboxCategoryId,
                $skyboxCommodityAttributeId
            );

            $rowsUpdateProductsCommodity = $this->updateProductSql(
                $productByCategory,
                $productIdsUpdate,
                $storeId,
                $skyboxCategoryId,
                $skyboxCommodityAttributeId,
                $noRepeat
            );

            $countProducts = $rowsInsertProductsCommodity + $rowsUpdateProductsCommodity + $rowsUpdateCategoriesSql;
            $this->messageManager->addSuccess(
                __(sprintf('%s products have been updated correctly.', $countProducts))
            );
        }

        return $result;
    }

    /**
     * @param $categoryId
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($categoryId)
    {
        $this->category = $this->categoryFactory->create()->load($categoryId);

        return $this->category;
    }

    /**
     * @param $categoryId
     * @param null $noRepeat
     * @param null $skyboxCategoryId
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getProductCollection($categoryId, $noRepeat = null, $skyboxCategoryId = null)
    {
        if ($this->category) {
            $cat = $this->category;
        } else {
            $cat = $this->getCategory($categoryId);
        }
        $cat = $cat->getProductCollection()->addAttributeToSelect('*');
        if ($noRepeat == true) {
            $cat = $cat->addAttributeToFilter('skybox_category_id', ['neq' => $skyboxCategoryId]);
        }

        return $cat;
    }

    /**
     * @param bool $asArray
     * @param bool $categoryId
     *
     * @return array|string
     */
    public function getAllChildren($asArray = false, $categoryId = false)
    {
        if ($this->category) {
            return $this->category->getAllChildren($asArray);
        } else {
            return $this->getCategory($categoryId)->getAllChildren($asArray);
        }
    }

    /**
     * @param bool $categoryId
     *
     * @return string
     */
    public function getChildren($categoryId = false)
    {
        if ($this->category) {
            return $this->category->getChildren();
        } else {
            return $this->getCategory($categoryId)->getChildren();
        }
    }

    /**
     * @param $category_id_array
     * @param null $noRepeat
     * @param null $skyboxCategoryId
     *
     * @return mixed
     */
    public function getProductAllCollection($category_id_array, $noRepeat = null, $skyboxCategoryId = null)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $category_id_array]);
        if ($noRepeat === true) {
            $collection->addAttributeToFilter('skybox_category_id', ['neq' => $skyboxCategoryId]);
        }

        return $collection;
    }

    /**
     * @return \Magento\Framework\App\ResourceConnection|null
     */
    public function getResourceConnection()
    {
        return $this->resourceConnection;
    }

    /**
     * @return void
     */
    public function getConnection()
    {
        $this->resourceConnection->getConnection();
    }

    /**
     * @param int $entity_type
     * @param string $name
     *
     * @return int|null
     */
    public function getSkyboxCommodityAttributeId($entity_type = 4, $name = 'skybox_category_id')
    {
        $resource   = $this->getResourceConnection();
        $connection = $resource->getConnection();
        $sql        = "SELE";
        $sql        .= "CT * FROM eav_attribute ";
        $sql        .= "WHERE attribute_code = '{$name}' ";
        $sql        .= " AND entity_type_id = {$entity_type}";

        $result = $connection->fetchRow($sql);

        $attributeId = null;

        if (!$result) {
            return 0;
        }

        $attributeId = $result['attribute_id'];

        return $attributeId;
    }

    /**
     * @param $categoryIds
     *
     * @return array
     */
    public function listProductByCategory($categoryIds)
    {
        $resource   = $this->getResourceConnection();
        $connection = $resource->getConnection();
        $tableName  = $resource->getTableName('catalog_category_product');
        $sql        = "SELE";
        $sql        .= "CT * FROM {$tableName} ";
        $sql        .= "WHERE category_id IN($categoryIds)";

        $result = $connection->fetchAssoc($sql);

        return $result;
    }

    /**
     * @param $attributeId
     *
     * @return array
     */
    public function listProductCommoditySkyboxByCategoryId($attributeId)
    {
        $resource   = $this->getResourceConnection();
        $connection = $resource->getConnection();
        $tableName  = $resource->getTableName('catalog_product_entity_int');
        $sql        = "SELE";
        $sql        .= "CT * FROM {$tableName} ";
        $sql        .= "WHERE attribute_id = $attributeId";

        $result = $connection->fetchAssoc($sql);

        return $result;
    }

    /**
     * @param $productIdsUpdate
     *
     * @return array
     */
    public function getProductIdsUpdate($productIdsUpdate)
    {
        $dataIds = [];
        if (!empty($productIdsUpdate)) {
            foreach ($productIdsUpdate as $prod) {
                $dataIds[] = $prod['entity_id'];
            }
        }

        return $dataIds;
    }

    /**
     * @param $products
     * @param $productIdsUpdate
     * @param $storeId
     * @param $categorySkyboxId
     * @param $attributeId
     *
     * @return int
     */
    public function insertProductSql($products, $productIdsUpdate, $storeId, $categorySkyboxId, $attributeId)
    {
        $sqlInsert = '';
        $sqlEmpty  = false;
        $resource  = $this->getResourceConnection();
        $tableName = $resource->getTableName('catalog_product_entity_int');
        if (!empty($products)) {
            $sqlInsert = "INSE";
            $sqlInsert .= "RT IGNORE INTO {$tableName} (attribute_id, store_id, entity_id, value) VALUES";
            foreach ($products as $prod) {
                if (!in_array($prod['product_id'], $productIdsUpdate)) {
                    $sqlEmpty  = true;
                    $sqlInsert .= "($attributeId, $storeId, {$prod['product_id']}, $categorySkyboxId),";
                }
            }
            $sqlInsert = rtrim($sqlInsert, ',');
        }

        $rowsCount = 0;

        if ($sqlEmpty) {
            $resource   = $this->getResourceConnection();
            $connection = $resource->getConnection();

            $resultInsert = $connection->query(/** @codingStandardsIgnoreLine MEQP2.Classes.ResourceModel.OutsideOfResourceModel */
                $sqlInsert
            );
            $rowsCount    = $resultInsert->rowCount();
        }

        return $rowsCount;
    }

    /**
     * @param $products
     * @param $productIdsUpdate
     * @param $storeId
     * @param $value
     * @param $attributeId
     * @param bool $noRepeat
     *
     * @return int
     */
    public function updateProductSql(
        $products,
        $productIdsUpdate,
        $storeId,
        $value,
        $attributeId,
        $noRepeat = false
    ) {
        $dataProductIds = [];
        if (!empty($products)) {
            foreach ($products as $prod) {
                if (in_array($prod['product_id'], $productIdsUpdate)) {
                    $dataProductIds[] = $prod['product_id'];
                }
            }
        }

        $productIds = implode(', ', $dataProductIds);

        $resource   = $this->getResourceConnection();
        $connection = $resource->getConnection();
        $tableName  = $resource->getTableName('catalog_product_entity_int');

        $data = ['value' => $value];

        $where = [
            'attribute_id = ?' => $attributeId,
            'store_id = ?'     => $storeId,
            'entity_id IN (?)' => $productIds
        ];

        if ($noRepeat) {
            $where = array_merge(['value <> ?' => $value], $where);
        }

        $rowsCount = $connection->update(
            $tableName,
            $data,
            $where
        );

        return $rowsCount;
    }

    /**
     * @param $categoryIds
     * @param $storeId
     * @param $attributeId
     * @param $value
     * @param bool $noRepeat
     *
     * @return int
     */
    public function updateCategoriesSql(
        $categoryIds,
        $storeId,
        $attributeId,
        $value,
        $noRepeat = false
    ) {
        $rowsCount = 0;

        $resource   = $this->getResourceConnection();
        $connection = $resource->getConnection();
        $tableName  = $connection->getTableName('catalog_category_entity_int');

        $data = ['value' => $value];

        $where = [
            'attribute_id = ?' => $attributeId,
            'store_id = ?'     => $storeId,
            'entity_id IN (?)' => $categoryIds
        ];

        if ($noRepeat) {
            $where = array_merge(['value <> ?' => $value], $where);
        }

        $rowsCount = $connection->update(
            $tableName,
            $data,
            $where
        );

        return $rowsCount;
    }
}
