<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use \Skybox\Checkout\Helper\Data;
use \Psr\Log\LoggerInterface;

class Commodities implements \Magento\Framework\Option\ArrayInterface
{

    public $options;
    private $dataHelper;

    public function __construct(
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger     = $logger;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = $this->getData();

        return $data;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            123 => __('Default Commodity - 123 '),
            124 => __('Another Commodity - 124 '),
        ];
    }

    private function getData()
    {
        $this->options = null;
        if (is_null($this->options)) {
            $data = [];

            $item = [
                'value' => '',
                'label' => '',
            ];

            $data[] = $item;

            try {
                $store       = $this->dataHelper->getClient();
                $commodities = $store->getCatalog()->commodities();

                foreach ($commodities as $commodity) {
                    $item   = [
                        'value' => $commodity->getId(),
                        'label' => $commodity->getDescription()
                    ];
                    $data[] = $item;
                }
            } catch (\Exception $e) {
                $this->logger->error('Error: Getting commodities.');
            }

            $this->options = $data;
        }

        return $this->options;
    }
}
