<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin\Product;

class BundlePrice
{
    private $helper;

    private $jsonEncoder;

    private $jsonDecoder;

    private $afterPriceBlock;

    /**
     * Constructor.
     *
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Skybox\Checkout\Block\Product\AfterPrice $afterPriceBlock
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
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject
     * @param $json
     *
     * @return mixed
     */
    public function afterGetJsonConfig(\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $subject, $json)
    {
        return $json;
    }
}
