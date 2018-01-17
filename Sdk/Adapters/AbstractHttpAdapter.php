<?php

namespace Skybox\Checkout\Sdk\Adapters;

/**
 * Class AbstractHttpAdapter
 * @package Skybox\Checkout\Sdk\Adapters
 */

abstract class AbstractHttpAdapter implements HttpAdapterInterface
{
    /**
     * @var string
     */
    private $url = '';

    /**
     * @var array
     */
    private $config = [];

    /**
     * HttpAdapter constructor
     *
     * @param array $config
     */
    public function __construct(/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        array $config = []
    ) {
        $this->config = $config;
        $this->url    = $config['url'];
    }

    /**
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/json',
    ];

    /**
     * @param array $headers
     */
    public function addHeader(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
