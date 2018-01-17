<?php

namespace Skybox\Checkout\Sdk\Exceptions;

/**
 * This is the error exception class.
 */
class SkyBoxErrorException extends SkyBoxException
{
    public function __construct($response)/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        $this->process($response);
        parent::__construct($this->getMessage(), $this->getCode());
    }
}
