<?php

namespace Skybox\Checkout\Sdk\Services\Contracts;

use Skybox\Checkout\Sdk\Services\SkyboxAuthService;

interface AuthenticateContractInterface
{

    /**
     * @param SkyboxAuthService $authService
     * @return mixed
     */
    public function setAuthenticateService(SkyboxAuthService $authService);
}
