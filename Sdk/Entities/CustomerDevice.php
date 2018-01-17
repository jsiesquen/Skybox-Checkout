<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class CustomerDevice
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method CustomerDevice setLocalIp(string $value)
 * @method CustomerDevice setRemoteIp(string $value)
 * @method CustomerDevice setProxy(string $value)
 * @method CustomerDevice setAgentBrowser(string $value)
 * @method CustomerDevice setLanguageBrowser(string $value)
 * @method string getLocalIp()
 * @method string getRemoteIp()
 * @method string getProxy()
 * @method string getAgentBrowser()
 * @method string getLanguageBrowser()
 */

class CustomerDevice extends AbstractEntity
{

    /**
     * @var string
     */
    public $localIp;

    /**
     * @var string
     */
    public $remoteIp;

    /**
     * @var string
     */
    public $proxy;

    /**
     * @var string
     */
    public $agentBrowser;

    /**
     * @var string
     */
    public $languageBrowser;
}
