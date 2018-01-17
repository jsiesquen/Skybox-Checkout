<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Block\InContext\Minicart;

use Magento\Checkout\Model\Session;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\ShortcutInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\Template\Context;

use \Skybox\Checkout\Helper\Data;

/**
 * Class Button
 */
class Button extends Template implements ShortcutInterface
{
    const ALIAS_ELEMENT_INDEX       = 'alias';
    const SKYBOX_BUTTON_ID          = 'skybox-checkout-in-context-checkout-main';
    const BUTTON_ELEMENT_INDEX      = 'button_id';
    const CART_BUTTON_ELEMENT_INDEX = 'add_to_cart_selector';
    const IMAGE_BUTTON_URL          = 'http://i.imgur.com/bjPjFxT.png';

    /**
     * @var string
     */
    private $imageButtonUrl;

    /**
     * @var bool
     */
    private $isMiniCart = false;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var MethodInterface
     */
    private $payment;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param ResolverInterface $localeResolver
     * @param Session $session
     * @param MethodInterface $payment
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ResolverInterface $localeResolver,
        Session $session,
        MethodInterface $payment,
        Data $dataHelper,
        $data = []
    ) {
        parent::__construct($context, $data);

        $this->localeResolver = $localeResolver;
        $this->payment        = $payment;
        $this->session        = $session;
        $this->dataHelper     = $dataHelper;
    }

    /**
     * @return bool
     */
    private function shouldRender()
    {
        if (!is_null($this->dataHelper->getClient())) {
            $allow = $this->dataHelper->allowed();
            if ($allow->isCartButtonSkyboxEnabled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        if (!$this->shouldRender()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        return $this->getData(self::BUTTON_ELEMENT_INDEX);
    }

    /**
     * @return string
     */
    public function getAddToCartSelector()
    {
        return $this->getData(self::CART_BUTTON_ELEMENT_INDEX);
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return self::IMAGE_BUTTON_URL;
    }

    public function getRedirectUrl()
    {
        $processUrl       = $this->getBaseUrl() . '/skbcheckout/process';
        $internationalUrl = $this->getBaseUrl() . '/skbcheckout/international';
        $returnUrl        = $processUrl . '?return_url=' . $internationalUrl;

        return $returnUrl;
    }

    /**
     * Get shortcut alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->getData(self::ALIAS_ELEMENT_INDEX);
    }

    /**
     * @param bool $isCatalog
     *
     * @return $this
     */
    public function setIsInCatalogProduct($isCatalog)
    {
        $this->isMiniCart = !$isCatalog;

        return $this;
    }
}
