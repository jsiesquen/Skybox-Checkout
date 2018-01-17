<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Block;

use Magento\Checkout\Model\Session\Proxy;
use \Magento\Framework\View\Element\Template\Context;
use \Skybox\Checkout\Helper\Data;
use \Skybox\Checkout\Helper\Config;

class Cart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Skybox\Checkout\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    public $assetRepository;

    /**
     * Cart constructor.
     *
     * @param Context $context
     * @param Data $dataHelper
     * @param Config $configHelper
     * @param \Magento\Checkout\Model\Session\Proxy $session
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        Config $configHelper,
        \Magento\Checkout\Model\Session\Proxy $session
    ) {
        parent::__construct($context);
        $this->dataHelper       = $dataHelper;
        $this->configHelper     = $configHelper;
        $this->checkoutSession  = $session;
        $this->assetRepository  = $context->getAssetRepository();
    }

    /**
     * @return \Magento\Framework\View\Asset\Repository
     */
    public function getAssetRepository()
    {
        return $this->assetRepository;
    }

    /**
     * @return mixed
     */
    public function getCart()
    {
        $client = $this->getClient();
        $result = $client->getNavigationBar();

        return $result;
    }

    /**
     * @return \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public function getClient()
    {
        return $this->dataHelper->getClient();
    }

    /**
     * @return Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }

    /**
     * @return bool
     */
    public function updateLocalStorage()
    {
        $localStorage = $this->checkoutSession->getUpdateLocalStorage();
        $localStorage = isset($localStorage) ? $localStorage : null;

        if ($localStorage) {
            $this->checkoutSession->setUpdateLocalStorage(0);

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCountryTitle()
    {
        $value = $this->getTitle();
        $title = isset($value) ? $this->getTitle() : 'Change country';

        return $title;
    }
}
