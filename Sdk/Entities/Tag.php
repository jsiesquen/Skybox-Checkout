<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Tag
 * @package Skybox\Checkout\Sdk\Entities
 * @method Tag setArrival(string $value)
 * @method Tag setOrdered(string $value)
 * @method Tag setDenied(string $value)
 * @method Tag setVerification(string $value)
 * @method string getArrival()
 * @method string getOrdered()
 * @method string getDenied()
 * @method string getVerification()
 */

class Tag extends AbstractEntity
{
    /**
     * @var string
     */
    public $arrival;

    /**
     * @var string
     */
    public $ordered;

    /**
     * @var string
     */
    public $denied;

    /**
     * @var string
     */
    public $verification;
}
