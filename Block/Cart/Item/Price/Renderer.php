<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Block\Cart\Item\Price;

class Renderer extends \Magento\Framework\View\Element\Template
{
    private $price;
    private $currencySymbol;

    public function setPrice($value)
    {
        $this->price = $value;
    }

    public function setCurrencySymbol($value)
    {
        $this->currencySymbol = $value;
    }

    public function toHtml()
    {
        $html = '<span class="price-excluding-tax" data-label="Excl. Tax">
                    <span class="cart-price">
                        <span class="price">' . $this->currencySymbol . $this->price . '</span>
                    </span></span>';

        return $html;
    }
}
