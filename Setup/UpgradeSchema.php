<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $version = $context->getVersion();
        $setup->startSetup();

        $quoteTable = 'quote';
        $orderTable = 'sales_order';

        // Quote table
        $setup->getConnection()
              ->addColumn(
                  $setup->getTable($quoteTable),
                  'skybox_fee',
                  [
                      'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                      'nullable' => true,
                      'length'   => '12,4',
                      'comment'  => 'SkyBox Fee Amount',
                  ]
              );

        // Order table
        $setup->getConnection()
              ->addColumn(
                  $setup->getTable($orderTable),
                  'skybox_fee',
                  [
                      'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                      'nullable' => true,
                      'length'   => '12,4',
                      'comment'  => 'SkyBox Fee Amount',
                  ]
              );

        $setup->endSetup();
    }
}
