<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @codingStandardsIgnoreFile */
namespace Skybox\Checkout\Adapters;

use Skybox\Checkout\Sdk\Adapters\HttpAdapterInterface;
use Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxNotAcceptableException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxUnauthorizedException;
use Skybox\Checkout\Sdk\Helpers\HttpCodes;

class HttpClient implements HttpAdapterInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var stdClass
     */
    private $dataResponse;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \stdClass $stdClass
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, \stdClass $stdClass) {
        $this->logger       = $logger;
        $this->dataResponse = $stdClass;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $url
     * @param array $headers
     *
     * @return mixed
     */
    public function get($url = '', array $headers = [])
    {
        $response = '';
        $error    = 0;

        try {
            $url = $this->getUrlService($url);

            $curlHeaders = ['Content-Type: text/json'];

            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    $curlHeaders[] = $name . ': ' . $value;
                }
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $responseBody = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = $this->buildResponseObj(
                $responseCode,
                $responseBody
            );
        } catch (\Exception $exception) {
            $error = 1;
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $json
     *
     * @return mixed
     */
    public function post($url = '', array $params = [], array $headers = [], $json = true)
    {
        $jsonV    = $json;
        $response = '';
        $error    = 0;

        try {
            $url = $this->getUrlService($url);

            $curlHeaders = ['Content-Type: text/json'];

            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    $curlHeaders[] = $name . ': ' . $value;
                }
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

            $responseBody = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = $this->buildResponseObj(
                $responseCode,
                $responseBody
            );
        } catch (\Exception $exception) {
            $error = 1;
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param bool $json
     *
     * @return stdClass|\stdClass|string
     */
    public function put($url = '', array $params = [], array $headers = [], $json = true)
    {
        $jsonV    = $json;
        $response = '';
        $error    = 0;

        try {
            $url = $this->getUrlService($url);

            $curlHeaders = ['Content-Type: text/json'];

            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    $curlHeaders[] = $name . ': ' . $value;
                }
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

            $responseBody = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = $this->buildResponseObj(
                $responseCode,
                $responseBody
            );
        } catch (\Exception $exception) {
            $error = 1;
        }

        return $response;
    }

    /**
     * @param string $url
     * @param array $headers
     *
     * @return mixed
     */
    public function delete($url = '', array $headers = [])
    {
        $response = '';
        $error    = 0;

        try {
            $url = $this->getUrlService($url);

            $curlHeaders = ['Content-Type: text/json'];

            if (!empty($headers)) {
                foreach ($headers as $name => $value) {
                    $curlHeaders[] = $name . ': ' . $value;
                }
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);

            $responseBody = curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response = $this->buildResponseObj(
                $responseCode,
                $responseBody
            );
        } catch (\Exception $exception) {
            $error = 1;
        }

        return $response;
    }

    /**
     * @param $response
     * @param bool $mustLog
     *
     * @return bool
     * @throws SkyBoxErrorException
     * @throws SkyboxNotAcceptableException
     * @throws SkyboxUnauthorizedException
     */
    public function parseResponse($response, $mustLog = false)
    {
        if ($mustLog) {
            $this->logger->debug(print_r([$response->status_code, $response->body], true));
        }
        switch ($response->status_code) {
            case HttpCodes::HTTP_OK:
                $contents = $this->parseContent($response);
                break;

            case HttpCodes::HTTP_CREATED:
                $contents = $this->parseContent($response);
                break;

            case HttpCodes::HTTP_NO_CONTENT:
                $contents = $this->parseContent($response);
                break;

            case HttpCodes::HTTP_BAD_REQUEST:
                throw new SkyBoxErrorException($response);
                break;

            case HttpCodes::HTTP_UNAUTHORIZED:
                throw new SkyboxUnauthorizedException($response->body);
                break;

            case HttpCodes::HTTP_NOT_ACCEPTABLE:
                throw new SkyboxNotAcceptableException($response->body);
                break;

            case HttpCodes::HTTP_INTERNAL_SERVER_ERROR:
                throw new SkyBoxErrorException($response->body);
                break;

            default:
                /*if (isset($response->body)) {
                    throw new SkyBoxErrorException($response->body);
                }*/
                throw new SkyBoxErrorException($response);
                break;
        }

        $content = true;
        if (isset($contents->Data)) {
            $content = $contents->Data;
        }

        return $content;
    }

    /**
     * @param string $response
     *
     * @return mixed
     */
    public function parseContent($response)
    {
        return json_decode($response->body);
    }

    /**
     * @param $resource
     *
     * @return string
     */
    private function getUrlService($resource)
    {
        $url = $this->config['url'];
        $url = "{$url}/{$resource}";

        return $url;
    }

    /**
     * @param $statusCode
     * @param $body
     *
     * @return stdClass|\stdClass
     */
    private function buildResponseObj($statusCode, $body)
    {
        $response              = $this->dataResponse;
        $response->status_code = $statusCode;
        $response->body        = $body;

        return $response;
    }
}
