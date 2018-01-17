<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Adapters\HttpAdapterInterface;

/***
 * Class AbstractService
 * @package Skybox\Checkout\Sdk\Services
 */

abstract class AbstractService
{

    /**
     * @var HttpAdapter
     */
    public $adapter;

    /**
     * AbstractService constructor.
     *
     * @param HttpAdapterInterface $adapter
     */
    public function __construct(HttpAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
}
