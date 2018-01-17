<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Entities\Token;
use Skybox\Checkout\Sdk\Transformers\KManagerItem;
use Skybox\Checkout\Sdk\Transformers\TokenTransformer;

/**
 * Class SkyboxAuthService
 * @package Skybox\Checkout\Sdk\Services
 */
class SkyboxAuthService extends AbstractService
{

    /**
     * @var null
     */
    public $merchantId = null;

    /**
     * @var null
     */
    public $merchantKey = null;

    /**
     * @var Token
     */
    public $token;

    /**
     * @param $merchantId
     * @param $merchantKey
     *
     * @return Token
     */
    public function authenticate($merchantId, $merchantKey)
    {
        $this->merchantId  = $merchantId;
        $this->merchantKey = $merchantKey;

        $params = [
            'Merchant' => [
                'Id'  => $this->merchantId,
                'Key' => $this->merchantKey,
            ],
        ];

        $httpResponse = $this->adapter->post('authenticate', $params);

        $response = $this->adapter->parseResponse($httpResponse);

        $itemT = new TokenTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $item = new KManagerItem($response, $itemT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        $this->token = $item->get();

        return $this->token;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return Token
     */
    public function refresh()
    {
        $this->token = $this->authenticate($this->merchantId, $this->merchantKey);

        return $this->token;
    }
}
