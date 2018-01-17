<?php
/**
 * Created by PhpStorm.
 * User: PC08
 * Date: 24/10/2017
 * Time: 11:32 AM
 */

namespace Skybox\Checkout\Sdk\Exceptions;

/***
 * Class SkyboxNotAcceptableException
 * @package Skybox\Exception
 */
class SkyboxNotAcceptableException extends SkyBoxException
{
    /**
     * SkyboxNotAcceptableException constructor.
     *
     * @param string $response
     */
    public function __construct($response) /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        $this->process($response);
        parent::__construct($this->getMessage(), $this->getCode());
    }
}