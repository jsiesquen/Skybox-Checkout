<?php

namespace Skybox\Checkout\Sdk;

use Skybox\Checkout\Sdk\Config\SkyboxConfig;
use Skybox\Checkout\Sdk\Entities\CustomerDevice;
use Skybox\Checkout\Sdk\Entities\Store;
use Skybox\Checkout\Sdk\Exceptions\SkyBoxException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxUnauthorizedException;
use Skybox\Checkout\Sdk\Services\SkyboxAuthService;
use Skybox\Checkout\Sdk\Services\SkyboxCartService;
use Skybox\Checkout\Sdk\Services\SkyboxCatalogService;
use Skybox\Checkout\Sdk\Services\SkyboxStoreService;

/**
 * Class SkyBoxApp
 * @package Skybox\Checkout
 */
class SkyBoxApp
{
    /**
     * @var string
     */
    public $defaultAdapter = 'rmccue';

    /**
     * @var array
     */
    public $adapters = [
        'rmccue' => "RmccueHttpAdapter",
    ];

    /**
     * @var AbstractHttpAdapter null
     */
    public $adapter = null;

    /**
     * @var null|SkyboxAuthService
     */
    public $authService = null;

    /**
     * @var null|SkyboxStoreService
     */
    public $storeService = null;

    /**
     * @var array
     */
    public $config = [];

    /**
     * SkyBoxApp constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
        /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        $this->_configValidate($config);
        $this->config['url'] = $this->_urlResolver($config);
        $this->adapter       = $this->_adapterResolver($this->config);
    }

    public function setHttpClientAdapter($adapter)
    {
        $this->adapter = $adapter;
        $this->adapter->setConfig($this->config);
    }

    public function destroyAdapter()
    {
        $this->adapter = null;
    }

    /**
     * @param CustomerDevice $customerDevice
     * @param bool $includeCart
     *
     * @return null|SkyboxStoreService
     * @throws SkyboxUnauthorizedException
     * @throws \Exception
     */
    public function storeUp(CustomerDevice $customerDevice, $includeCart = true)
    {
        $this->authService = new SkyboxAuthService($this->adapter);
        /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        $catch = 0;
        try {
            $merchantId  = $this->config['merchantId'];
            $merchantKey = $this->config['merchantKey'];

            // authenticate
            $this->authService->authenticate($merchantId, $merchantKey);

            // create store entity
            $store = new Store($merchantId, $merchantKey);
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

            // set services
            $this->storeService = new SkyboxStoreService($this->adapter);
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $this->storeService->setEntity($store);
            $this->storeService->setAuthenticateService($this->authService);

            $catalogService = new SkyboxCatalogService($this->adapter);
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $catalogService->setAuthenticateService($this->authService);
            $catalogService->setStore($store);
            $this->storeService->setCatalogService($catalogService);

            if ($includeCart) {
                $cartService = new SkyboxCartService($this->adapter);
                /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $cartService->setAuthenticateService($this->authService);
                $cartService->setStore($store);
                $this->storeService->setCartService($cartService);

                $this->storeService->getCart()->create($customerDevice);
            }

            return $this->storeService;
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            throw new SkyboxUnauthorizedException("Error unauthorized: " . $skyboxUnauthorizedException);
        } catch (\Exception $exception) {
            throw new \Exception("Error trying to access the Skybox Checkout service: " . $exception);
        }
    }

    /**
     * @return SkyboxStoreService
     */
    public function  refreshCart(){
        $this->storeService->getCart()->getInfo();

        return $this->storeService;
    }

    /**
     * @return StoreService
     */
    public function getStore()
    {
        return $this->storeService;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function _urlResolver($config = [])
    {
        $url = SkyboxConfig::URL_PRODUCTION;
        if (isset($config['debug']) && $config['debug'] == true) {
            $url = SkyboxConfig::URL_BETA;
        }

        return $url;
    }

    /**
     * @param array $config
     *
     * @throws SkyBoxException
     */
    private function _configValidate(array $config = [])
    {
        $fields = [
            'merchantId',
            'merchantKey'
        ];

        foreach ($fields as $index => $field) {
            if (!isset($config[$field])) {
                throw new SkyBoxException("The {$field} is required.");
            }
        }

        $this->config = $config;
    }

    /**
     * @param $name
     * @param array $config
     *
     * @return HttpAdapter|null
     */
    private function _createAdapter($name, $config = [])
    {
        $nameV   = $name;
        $configV = $config;
        $adapter = null;

        return $adapter;
    }

    /**
     * @param array $config
     *
     * @return null|HttpAdapter
     */
    private function _adapterResolver(array $config = [])
    {
        $currentAdapter = $this->defaultAdapter;
        if (!empty($config) && isset($config['adapter']) && array_key_exists($config['adapter'], $this->adapters)) {
            $currentAdapter = $this->adapters[$config['adapter']];
        }

        $this->adapter = $this->_createAdapter($currentAdapter, $config);

        return $this->adapter;
    }
}
