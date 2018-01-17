<?php

namespace Skybox\Checkout\Sdk\Transformers;

use Skybox\Checkout\Sdk\Entities\CustomerDevice;

/***
 * Class CustomerDeviceSkyboxTransformer
 * @package Skybox\Checkout\Sdk\Transformers
 */
class CustomerDeviceSkyboxTransformer extends KTransformer
{

    /**
     * @param CustomerDevice $customerDevice
     *
     * @return array
     */
    public function transform(CustomerDevice $customerDevice)
    {
        return [
            "Ip" => [
                "Local"  => $customerDevice->getLocalIp(),
                "Remote" => $customerDevice->getRemoteIp(),
                "Proxy"  => $customerDevice->getProxy()
            ],
            "Browser" => [
                "Agent"     => $customerDevice->getAgentBrowser(),
                "Languages" => $customerDevice->getLanguageBrowser()
            ]
        ];
    }
}
