<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Helper;

class Config extends AbstractHelper
{
    const TYPE_LOCATION_ALLOW_STORE         = 1;
    const TYPE_LOCATION_ALLOW_CART_DISABLE  = 0;
    const TYPE_LOCATION_ALLOW_CART_SHOW     = 1;
    const TYPE_LOCATION_ALLOW_CART_HIDE     = 3;
    const FULL_ACTION_NAME_IFRAME_SUCCESS   = 'skbcheckout_international_success';
    const FULL_ACTION_NAME_IFRAME           = 'skbcheckout_international_index';

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getMerchantId()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_merchant_id');
    }

    public function getUrlClient()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_url_client');
    }

    public function getUrlApi()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_url_api');
    }

    public function getEmail()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_email');
    }

    public function getApiResponse()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_api_response');
    }

    public function getWeightUnit()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_weight_unit');
    }

    public function getMerchantKey()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_merchant_key');
    }

    public function getEnabled()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_enable_frontend');
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return deprecated
     */
    public function getTypeLocationAllow()
    {
        return $this->getConfig('skyboxcheckout/settings/skb_store_type');
    }
}
