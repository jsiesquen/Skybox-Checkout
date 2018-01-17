<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Entities\Store;
use Skybox\Checkout\Sdk\Entities\Token;
use Skybox\Checkout\Sdk\Services\Contracts\AuthenticateContractInterface;

class SkyboxService extends AbstractService implements AuthenticateContractInterface
{

    /**
     * @var Store
     */
    public $store;

    /**
     * @var SkyboxAuthService
     */
    public $authService;

    /**
     * @var array
     */
    public $headers = [];

    /**
     * @param Store $store
     */
    public function setStore(Store $store)
    {
        $this->store = $store;
    }

    /**
     * @param SkyboxAuthService $authService
     * @return mixed
     */
    public function setAuthenticateService(SkyboxAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     *
     */
    public function refreshToken()
    {
        $this->authService->refresh();
        $this->headers = [];
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->authService->getToken();
    }

    /**
     * @return array
     */
    public function getDefaultHeaders()
    {
        $headers = [
            'Authorization' => $this->authService->getToken()->getHash(),
            'X-Skybox-Merchant-Id' => $this->store->getId(),
        ];

        return $headers;
    }

    /**
     * @param array $headers
     */
    public function addHeader(array $headers = [])
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        $this->headers = array_merge($this->getDefaultHeaders(), $this->headers);

        return $this->headers;
    }

    /**
     * @return array
     */
    public function getCartHeaders()
    {
        $this->addHeader([
            'X-Skybox-Cart-Id' => $this->store->getCart()->getId()
        ]);

        return $this->getHeaders();
    }
}
