<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Skybox\Checkout\Model\Config\Source;

class StoreType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => '1', 'label' => __('Store Type 1')], ['value' => '3', 'label' => __('Store Type 3')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['1' => __('Store Type 1'), '3' => __('Store Type 3')];
    }
}
