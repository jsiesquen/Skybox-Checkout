<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $contextVar = $context;
        $installer  = $setup;

        $installer->startSetup();

        $tableName = $installer->getTable('skybox_log_service');

        if ($installer->getConnection()->isTableExists($tableName) != true) {

            /**
             * Create table 'skybox_log_service'
             */
            $table = $installer->getConnection()
                               ->newTable($tableName)
                               ->addColumn(
                                   'id',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                   null,
                                   ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                                   'Service Id'
                               )
                               ->addColumn(
                                   'action',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                   32,
                                   ['nullable' => false],
                                   'Action Name'
                               )
                               ->addColumn(
                                   'request',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                   255,
                                   ['nullable' => false],
                                   'Request Id'
                               )
                               ->addColumn(
                                   'response',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                   null,
                                   ['nullable' => false],
                                   'Response Value'
                               )
                               ->addColumn(
                                   'created_at',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                   null,
                                   ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                                   'Created At'
                               )
                               ->addColumn(
                                   'updated_at',
                                   \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                   null,
                                   [],
                                   'Updated At'
                               )
                               ->addIndex(
                                   $installer->getIdxName($tableName, ['id']),
                                   'id'
                               )
                               ->setComment(
                                   'SkyBox Checkout Service Log'
                               );
            $installer->getConnection()->createTable($table);
        }
    }
}
