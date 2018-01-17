<?php

namespace Skybox\Checkout\Sdk\Adapters;

/**
 * Interface HttpAdapterInterface
 * @package Skybox\Checkout\Sdk\Adapters
 */

interface HttpAdapterInterface
{

    /**
     * @param string $url
     * @param array $headers
     *
     * @return mixed
     */
    public function get($url = "", array $headers = []);

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $json
     *
     * @return mixed
     */
    public function post($url = "", array $params = [], array $headers = [], $json = true);

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $json
     *
     * @return mixed
     */
    public function put($url = "", array $params = [], array $headers = [], $json = true);

    /**
     * @param string $url
     * @param array $headers
     *
     * @return mixed
     */
    public function delete($url = "", array $headers = []);

    /**
     * @param $response
     *
     * @return mixed
     */

    public function parseResponse($response, $mustLog = false);
}
