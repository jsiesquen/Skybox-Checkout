<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Entity\Attribute\Source;

class Commodity extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public $options;
    private $dataHelper;
    private $logger;

    /**
     * Commodity constructor.
     *
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
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

    public function getAllOptions()
    {
        return $this->toOptionArray();
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
            } catch (\Exception $exception) {
                $this->logger->debug("[SBC] Commodity::getData (exception): " . $exception->getMessage());
            }

            $this->options = $data;
        }

        return $this->options;
    }
}
