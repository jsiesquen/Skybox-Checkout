<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model;

use Zend\Serializer\Adapter as SerializerAdapter;
use Zend\Serializer\Exception;
use Zend\Serializer\Serializer;

/**
 * Adapter
 * @package Skybox\Checkout\Model
 */
class Adapter
{
    const TIME_REFRESH = 600;

    private $merchant_id;
    private $merchant_key;
    private $session_static;
    private $token;
    private $guid;
    private $auth;
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    private $checkoutSession;

    /**
     * @var \Skybox\Checkout\Helper\Config
     */
    public $config;

    /**
     * Adapter constructor.
     *
     * @param LogServiceFactory $logServiceFactory
     * @param \Skybox\Checkout\Helper\Config $config
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Session\SessionManagerInterface $customerSession
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Skybox\Checkout\Model\LogServiceFactory $logServiceFactory,
        \Skybox\Checkout\Helper\Config $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Session\SessionManagerInterface $customerSession,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->logServiceFactory = $logServiceFactory;
        $this->config            = $config;
        $this->customerSession   = $customerSession;
        $this->logger            = $logger;
        $this->checkoutSession   = $checkoutSession;
    }

    /**
     * @return mixed|null
     */
    public function getStoreObject()
    {
        $value = $this->checkoutSession->getStoreObject();
        if (!empty($value)) {
            $result = unserialize($value); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

            return $result;
        }

        return null;
    }

    /**
     * @param $value
     */
    public function setStoreObject($value)
    {
        $result = serialize($value);
        $this->checkoutSession->setStoreObject($result);
    }

    public function refreshCart(){
    }

    /**
     * @return \Skybox\Checkout\Sdk\Entities\CartDetail
     */
    public function getCartDetails()
    {
        $value = $this->checkoutSession->getCartDetails();

        return $value;
    }

    /**
     * @param $value
     */
    public function setCartDetails($value)
    {
        $this->checkoutSession->setCartDetails($value);
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @deprecated
     *
     * @param mixed $merchant_id
     */
    public function setMerchantId($merchant_id)
    {
        $this->checkoutSession->setMerchantId($merchant_id);
        $this->merchant_id = $merchant_id;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getMerchantKey()
    {
        return $this->merchant_key;
    }

    /**
     * @deprecated
     *
     * @param mixed $merchant_key
     */
    public function setMerchantKey($merchant_key)
    {
        $this->checkoutSession->setMerchantKey($merchant_key);
        $this->merchant_key = $merchant_key;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getSessionStatic()
    {

        if (!$this->session_static) {
            $this->session_static = $this->checkoutSession->getSessionStatic();
        }

        return $this->session_static;
    }

    /**
     * @deprecated
     *
     * @param mixed $session_static
     */
    public function setSessionStatic($session_static)
    {
        $this->checkoutSession->setSessionStatic($session_static);
        $this->session_static = $session_static;
    }

    /*
     * @deprecated
     */
    public function compareSession($value)
    {
        $visitor_ = $this->customerSession->getVisitorData();
        if (strtotime($visitor_['last_visit_at']) > strtotime($value) + self::TIME_REFRESH) {
            $this->setSessionStatic(date('Y-m-d H:i:s'));

            return true;
        } else {
            return false;
        }
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getToken()
    {
        if (!$this->token) {
            $this->token = $this->checkoutSession->getToken();
        }

        return $this->token;
    }

    /**
     * @deprecated
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->checkoutSession->setToken($token);
        $this->token = $token;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getGuid()
    {
        if (!$this->guid) {
            $guid = $this->checkoutSession->getGuid();

            $this->guid = $guid;
        }

        return $this->guid;
    }

    /**
     * @deprecated
     *
     * @param mixed $guid
     */
    public function setGuid($guid)
    {
        $this->checkoutSession->setGuid($guid);
        $this->guid = $guid;
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getAuth()
    {
        if (!$this->auth) {
            $this->auth = $this->checkoutSession->getSkyboxAuth();
        }

        return $this->auth;
    }

    /**
     * @deprecated
     *
     * @param mixed $value
     */
    public function setAuth($value)
    {
        $this->checkoutSession->setSkyboxAuth($value);
        $this->auth = $value;
    }

    /**
     * @deprecated
     */
    public function filterProduct(array $data)
    {
        return $data;
    }

    /**
     * @param $action
     * @param $request
     * @param $response
     */
    public function saveApiResponse($action, $request, $response)
    {
        if (!$this->config->getApiResponse()) {
            return;
        }

        try {
            $logService = $this->logServiceFactory->create();
            $logService->setData('action', $action);
            $logService->setData('request', $request);
            $logService->setData('response', json_encode($response));
            $logService->setData('updated_at', time());
            $logService->save();
        } catch (\Exception $exception) {
            $this->logger->debug("[SBC] Adapter::saveApiResponse (exception): " . $exception->getMessage());
        }
    }
}
