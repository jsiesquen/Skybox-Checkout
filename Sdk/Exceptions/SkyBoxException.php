<?php

namespace Skybox\Checkout\Sdk\Exceptions;

use Skybox\Checkout\Sdk\Config\ApiError;
use Exception;

/***
 * SkyBoxException
 *
 * @package Skybox\Exceptions
 */
class SkyBoxException extends Exception
{
    public $code;
    public $message;

    /**
     * @param $httpResponse
     */
    public function process($httpResponse)
    {
        $apiError = new ApiError();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $error    = $apiError->getDefaultError();

        if (is_string($httpResponse)) {
            $response = json_decode($httpResponse, true);
        } elseif (isset($httpResponse->body)) {
            $response = json_decode($httpResponse->body, true);
        }

        if (isset($response)) {
            $errorResponse = reset($response['Errors']);
            $error         = $apiError->getError($errorResponse['Code']);
        }

        $this->code    = $error['code'];
        $this->message = $error['message'];
    }
}
