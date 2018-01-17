<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Skybox\Checkout\Model\Config\Source;

class Measureofunit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'LBS', 'label' => __('LBS')], ['value' => 'KGS', 'label' => __('KGS')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['LBS' => __('LBS'), 'KGS' => __('KGS')];
    }
}
