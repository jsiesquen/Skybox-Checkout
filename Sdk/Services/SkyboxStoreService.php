<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Entities\Country;
use Skybox\Checkout\Sdk\Entities\Currency;
use Skybox\Checkout\Sdk\Entities\Store;
use Skybox\Checkout\Sdk\Entities\Template;

/**
 * Class SkyboxStoreService
 * @package Skybox\Checkout\Sdk\Services
 */
class SkyboxStoreService extends SkyboxService
{

    /**
     * @var SkyboxCartService
     */
    public $cartService;

    /**
     * @var SkyboxCatalogService
     */
    public $catalogService;

    /**
     * @var Store
     */
    public $store;

    /**
     * @param SkyboxCartService $cartService
     */
    public function setCartService(SkyboxCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @param SkyboxCatalogService $catalogService
     */
    public function setCatalogService(SkyboxCatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getEntity()->getId();
    }

    /**
     * @return SkyboxCartService
     */
    public function getCart()
    {
        return $this->cartService;
    }

    /**
     * @return SkyboxCatalogService
     */
    public function getCatalog()
    {
        return $this->catalogService;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->getEntity()->getTemplate();
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->getEntity()->getCurrency();
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->getEntity()->getCountry();
    }

    /**
     * @return Store
     */
    public function getEntity()
    {
        return $this->store;
    }

    /**
     * @param Store $store
     */
    public function setEntity(Store $store)
    {
        $this->store = $store;
    }

    /**
     *
     */
    public function syncUp()
    {
        $this->cartService->getInfo();
    }

    public function getTag()
    {
        return $this->getEntity()->getTag();
    }
}
