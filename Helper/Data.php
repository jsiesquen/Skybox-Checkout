<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Helper;

use Magento\Framework\App\Helper\Context;
use Skybox\Checkout\Sdk\Entities\CustomerDevice;
use Skybox\Checkout\Sdk\Exceptions\SkyBoxException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxUnauthorizedException;
use Skybox\Checkout\Sdk\SkyBoxApp;

/**
 * Data Helper
 * @package Skybox\Checkout\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public $client;

    private $context;
    private $helperConfig;
    public $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @return \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public $remoteAddress;

    /**
     * @var \Skybox\Checkout\Model\Adapter
     */
    public $adapter;

    /**
     * @var Allow
     */
    public $allowHelper;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    public $httpHeader;

    /**
     * @var \Skybox\Checkout\Sdk\Entities\CartDetail|null
     */
    public $cartDetails;

    /**
     * @var \Skybox\Checkout\Adapters\HttpClient
     */
    public $httpClientAdapter;

    /**
     * @var CustomerDevice
     */
    public $customerDevice;

    public function __construct(
        Context $context,
        Config $helperConfig,
        \Skybox\Checkout\Model\Adapter $adapter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Skybox\Checkout\Sdk\Helpers\Allow $allowHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Skybox\Checkout\Adapters\HttpClient $httpClientAdapter,
        \Skybox\Checkout\Sdk\Entities\CustomerDevice $customerDevice
    ) {
        $this->context           = $context;
        $this->helperConfig      = $helperConfig;
        $this->adapter           = $adapter;
        $this->request           = $context->getRequest();
        $this->storeManager      = $storeManager;
        $this->checkoutSession   = $checkoutSession;
        $this->remoteAddress     = $context->getRemoteAddress();
        $this->allowHelper       = $allowHelper;
        $this->httpHeader        = $context->getHttpHeader();
        $this->curl              = $curl;
        $this->httpClientAdapter = $httpClientAdapter;
        $this->customerDevice    = $customerDevice;
        parent::__construct($context, $storeManager);
    }

    /**
     * Get SkyBOX Checkout Client
     *
     * @param int $refresh
     *
     * @return \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public function getClient($refresh = 0)
    {
        $this->client = $this->adapter->getStoreObject();

        try {
            if (is_null($this->client)) {
                $merchantId  = $this->helperConfig->getMerchantId();
                $merchantKey = $this->helperConfig->getMerchantKey();

                $config = [
                    'adapter'     => 'magento2',
                    'merchantId'  => $merchantId,
                    'merchantKey' => $merchantKey
                ];
                $customerDevice = $this->getCustomerDevice();

                $skyBox = new SkyBoxApp($config); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $skyBox->setHttpClientAdapter($this->httpClientAdapter);
                $this->client = $skyBox->storeUp($customerDevice);
                $this->adapter->setStoreObject($this->client);

                return $this->client;
            } elseif ($refresh === 1) {
                $this->checkoutSession->setUpdateLocalStorage(1);
                $this->cleanAuth($this->client);
                $this->adapter->setStoreObject($this->client);
            }
            return $this->client;
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            return null;
        } catch (\Exception $generalException) {
            return null;
        }
    }

    /**
     * Refresh Cart data
     *
     * @return \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public function refreshCart() {
        $this->client->getCart()->getInfo();
        return $this->client;
    }

    /**
     * Get SkyBOX Checkout Admin Client
     *
     * @return \Skybox\Checkout\Sdk\Services\SkyboxStoreService
     */
    public function getAdminClient()
    {
        $client = null;

        try {
            if (is_null($client)) {
                $config = [
                    'adapter'     => 'magento2',
                    'merchantId'  => $this->helperConfig->getMerchantId(),
                    'merchantKey' => $this->helperConfig->getMerchantKey()
                ];

                $customerDevice = $this->getCustomerDevice();

                $skyBox = new SkyBoxApp($config); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $skyBox->setHttpClientAdapter($this->httpClientAdapter);
                $client = $skyBox->storeUp($customerDevice, false);
            }

            return $client;
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            return null;
        } catch (\Exception $generalException) {
            return null;
        }
    }

    /**
     * @param null $client
     */
    public function cleanAuth($client = null)
    {
        if (!$client) {
            $client = $this->getClient();
        }

        $this->refreshCart();
        $this->checkoutSession->unsCartDetails();
        $this->adapter->setStoreObject($client);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        $client = $this->getClient();
        $result = $client->getEntity()->getLocationAllow();

        return boolval($result);
    }

    /**
     * @return string
     */
    public function getNavigationBar()
    {
        $fullActionName = $this->request->getFullActionName();

        if ($fullActionName === \Skybox\Checkout\Helper\Config::FULL_ACTION_NAME_IFRAME_SUCCESS)
        {
            $result         = $this->checkoutSession->getNavigationBar();
            $this->checkoutSession->unsNavigationBar();
        }
        else {
            $checkoutLoaded = $this->checkoutSession->getSkyboxCheckoutLoaded();
            $store          = $this->getClient($checkoutLoaded);
            $result         = $store->getTemplate()->getNavBar();
            if ($fullActionName !== \Skybox\Checkout\Helper\Config::FULL_ACTION_NAME_IFRAME)
            {
                $this->checkoutSession->setSkyboxCheckoutLoaded(0);
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getCssVersion()
    {
        $store  = $this->getClient();
        $result = $store->getTemplate()->getVersion();

        return $result;
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        $store  = $this->getClient();
        $result = $store->getEntity()->getCode();

        return $result;
    }

    /**
     * @return int
     */
    public function getIntegrationType()
    {
        $store  = $this->getClient();
        $result = $store->getEntity()->getIntegrationType();

        return $result;
    }

    /**
     * @return int
     */
    public function getLocationAllow()
    {
        $store  = $this->getClient();
        $result = $store->getEntity()->getLocationAllow();

        return $result;
    }

    /**
     * @return string
     */
    public function getCartDataURL()
    {
        $store  = $this->getClient();
        $result = $store->getCart()->getEntity()->getDataUrl();

        return $result;
    }

    /**
     * @return null|\Skybox\Checkout\Sdk\Helpers\Allow
     */
    public function allowed()
    {
        $integrationType = $this->getIntegrationType();
        $locationAllow   = $this->getLocationAllow();
        $fullActionName  = $this->request->getFullActionName();

        $this->allowHelper->setIntegrationType($integrationType);
        $this->allowHelper->setLocationAllow($locationAllow);
        $this->allowHelper->setActionName($fullActionName);

        return $this->allowHelper;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        $store  = $this->getClient();
        $result = $store->getCurrency()->getIso() . ' ';
        if (!$result) {
            $result = "$";
        }

        return $result;
    }

    /**
     * @return \Skybox\Checkout\Sdk\Entities\CartDetail|null
     */
    public function getSkyboxCartDetails()
    {
        $this->cartDetails = $this->adapter->getCartDetails();

        if (!$this->cartDetails) {
            $cartDetails = null;
            $client      = $this->getClient();

            if ($client->getCart()->getTotalItems() === 0) {
                return null;
            }

            $cartDetails = $client->getCart()->getConcepts();
            $this->adapter->setCartDetails($cartDetails);
            $this->cartDetails = $cartDetails;
        }

        return $this->cartDetails;
    }

    /**
     * @return float|int
     */
    public function getSkyboxFee()
    {
        $cartDetails = $this->getSkyboxCartDetails();
        $skyboxFee   = 0;

        if (!$cartDetails) {
            return $skyboxFee;
        }

        $concepts = $cartDetails->getConcepts();

        if (!empty($concepts)) {
            foreach ($concepts as $concept) {
                /** @var \Skybox\Checkout\Sdk\Entities\Concept $concept */
                $value     = $concept->getPrice();
                $skyboxFee += $value;
            }
        }

        return $skyboxFee;
    }

    /**
     * @return float|null
     */
    public function getSkyboxSubtotal()
    {
        $cartDetails = $this->getSkyboxCartDetails();
        $price       = null;

        if ($cartDetails) {
            $price = $cartDetails->getInternationalCharge()->getPrice();
        }

        return $price;
    }

    /**
     * @return float|null
     */
    public function getSkyboxGrandTotal()
    {
        $cartDetails = $this->getSkyboxCartDetails();
        $price       = null;

        if ($cartDetails) {
            $price = $cartDetails->getInternationalCharge()->getTotal();
        }

        return $price;
    }

    /**
     * @return array
     */
    public function getCustomerData()
    {
        $remoteAddress      = $this->request->getServer('REMOTE_ADDR');
        $httpAcceptLanguage = $this->request->getServer('HTTP_ACCEPT_LANGUAGE');

        $result = [
            'customeriplocal'   => $this->getUserIP(),
            'customeripremote'  => $remoteAddress,
            'customeripproxy'   => '',
            'customerbrowser'   => $this->getUserAgent(),
            'customerlanguages' => $httpAcceptLanguage,
        ];

        return $result;
    }

    /**
     * @return void
     */
    public function visitCountry() {
        $this->_logger->debug('[SBC] Data::visitCountry:');
        $this->checkoutSession->setVisitCheckout(true);
    }

    /**
     * @return void
     */
    public function verifyCountry() {
        $this->_logger->debug('[SBC] Data::verifyCountry:');
        $this->getClient()->syncUp();
        $this->checkoutSession->setVisitCheckout(false);
    }

    /**
     * @return string
     */
    private function getUserIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @return string
     */
    private function getUserAgent()
    {
        return $this->httpHeader->getHttpUserAgent();
    }

    /**
     * @return \Skybox\Checkout\Sdk\Entities\CustomerDevice
     */
    private function getCustomerDevice()
    {
        $this->_logger->debug('[SBC] Data:getCustomerDevice');
        $remoteAddress      = $this->request->getServer('REMOTE_ADDR');
        $httpAcceptLanguage = $this->request->getServer('HTTP_ACCEPT_LANGUAGE');

        $customerDevice = $this->customerDevice;
        $customerDevice->setLocalIp($this->getUserIp());
        $customerDevice->setRemoteIp($remoteAddress);
        $customerDevice->setProxy('');
        $customerDevice->setAgentBrowser($this->getUserAgent());
        $customerDevice->setLanguageBrowser($httpAcceptLanguage);

        return $customerDevice;
    }
}
