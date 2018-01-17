<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Product;

class ConfigurablePrice
{
    /**
     * @var \Skybox\Checkout\Helper\Product\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    private $afterPriceBlock;

    /**
     * Constructor
     *
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
        \Skybox\Checkout\Helper\Product\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Skybox\Checkout\Block\Product\AfterPrice $afterPriceBlock
    ) {
        $this->afterPriceBlock = $afterPriceBlock;
        $this->helper          = $helper;
        $this->jsonEncoder     = $jsonEncoder;
        $this->jsonDecoder     = $jsonDecoder;
    }

    /**
     * Plugin for configurable price rendering. Iterates over configurable's simples and adds the base price
     * to price configuration.
     *
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param $json
     *
     * @return mixed
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $json
    ) {
        return $json;
    }
}
