<?php

namespace Skybox\Checkout\Sdk\Exceptions;

/***
 * Class SkyboxUnauthorizedException
 * @package Skybox\Exception
 */

class SkyboxUnauthorizedException extends SkyBoxException
{
    /**
     * SkyboxUnauthorizedException constructor.
     *
     * @param string $response
     */
    public function __construct($response) /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        $this->process($response);
        parent::__construct($this->getMessage(), $this->getCode());
    }
}
