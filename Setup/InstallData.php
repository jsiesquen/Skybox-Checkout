<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->eavSetupFactory   = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $contextVar = $context;
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the Category - eav/attribute
         */
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'skybox_category_id'
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'skybox_category_id',
            [
                'type'                    => 'int',
                'input'                   => 'select',
                'label'                   => 'Commodity Name',
                'source'                  => 'Skybox\Checkout\Model\Entity\Attribute\Source\Commodity',
                'required'                => false,
                'user_defined'            => true,
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'                   => 'SkyBox Checkout',
                'used_in_product_listing' => true,
            ]
        );

        /**
         * Add attributes to the Product - eav/attribute
         */

        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'skybox_category_id'
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'skybox_category_id',
            [
                'type'                    => 'int',
                'input'                   => 'select',
                'label'                   => 'Commodity Name',
                'source'                  => 'Skybox\Checkout\Model\Entity\Attribute\Source\Commodity',
                'required'                => false,
                'user_defined'            => true,
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group'                   => 'SkyBox Checkout',
                'used_in_product_listing' => true,
            ]
        );

        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);

        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        /**
         * Install eav entity types to the eav/entity_type table
         */
        $attributes = [
            'skybox_product_id' => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_price'      => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_customs'    => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_shipping'   => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_insurance'  => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_total'      => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],

            'skybox_price_usd'     => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_customs_usd'   => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_shipping_usd'  => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_insurance_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_total_usd'     => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_guid'          => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_row_total'     => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],

            'skybox_base_price'       => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_base_price_usd'   => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_adjust_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_adjust_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_adjust_label'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_concepts'         => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_rmt'              => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
        ];

        // Table: sales_flat_quote_item
        // Table: sales_flat_order_item

        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote_item', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('order_item', $attributeCode, $attributeParams);
        }

        /**
         * Install eav entity types to the eav/entity_type table
         */
        $attributes = [
            'skybox_subtotal'         => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_base_subtotal'    => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_grand_total'      => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],
            'skybox_base_grand_total' => ['type' => Table::TYPE_TEXT, 'visible' => false, 'required' => false],

            'skybox_customs_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_customs_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_taxes_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_taxes_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_handling_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_handling_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_shipping_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_shipping_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_insurance_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_insurance_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_clearence_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_clearence_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_duties_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_duties_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_others_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_others_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_adjust_total'     => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_adjust_total_usd' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],

            'skybox_concepts' => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
            'skybox_rmt'      => ['type' => Table::TYPE_TEXT, 'visible' => true, 'required' => false],
        ];

        // Table: sales_flat_quote_address
        // Table: sales_flat_order

        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote_address', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('order', $attributeCode, $attributeParams);
        }

        $setup->endSetup();
    }
}
