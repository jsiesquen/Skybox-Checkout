<?php

namespace Skybox\Checkout\Sdk\Helpers;

/**
 * Allow Helper
 *
 * @package Skybox\Checkout\Sdk\Helpers
 */
class Allow
{
    const TYPE_LOCATION_ALLOW_STORE = 1;
    const TYPE_LOCATION_ALLOW_CART_DISABLE = 0;
    const TYPE_LOCATION_ALLOW_CART_SHOW = 1;
    const TYPE_LOCATION_ALLOW_CART_HIDE = 3;
    const FULL_ACTION_NAME_IFRAME = 'skbcheckout_international_index';

    private $integrationType;
    private $locationAllow;
    private $actionName;

    /**
     * @return int
     */
    public function getIntegrationType()
    {
        return $this->integrationType;
    }

    /**
     * @param int $integrationType
     */
    public function setIntegrationType($integrationType)
    {
        $this->integrationType = (int)$integrationType;
    }

    /**
     * @return int
     */
    public function getLocationAllow()
    {
        return $this->locationAllow;
    }

    /**
     * @param int $locationAllow
     */
    public function setLocationAllow($locationAllow)
    {
        $this->locationAllow = (int)$locationAllow;
    }

    /**
     * @return mixed
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param mixed $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * Constructor.
     *
     * @param int $integrationType
     * @param int $locationAllow
     * @param null $fullActionName
     */
    public function __construct($integrationType = 1, $locationAllow = 0, $fullActionName = null)
    {
        $this->integrationType = $integrationType;
        $this->locationAllow   = $locationAllow;
        $this->actionName      = $fullActionName;
    }

    /**
     * Returns the Price Button visibility
     *
     * @return bool
     */
    public function isPriceEnabled()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * Returns if the Change Country Navigation Bar visibility
     * @return bool
     */
    public function isCartBarEnabled()
    {
        $fullActionName = $this->getActionName();

        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
            && $fullActionName != self::FULL_ACTION_NAME_IFRAME
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
                  && $fullActionName == self::FULL_ACTION_NAME_IFRAME
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
                  && $fullActionName != self::FULL_ACTION_NAME_IFRAME
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
                  && $fullActionName == self::FULL_ACTION_NAME_IFRAME
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * Is the Cart Button is enabled
     * @return bool
     */
    public function isCartButtonEnabled()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * If the Skybox Payment Button is enabled
     *
     * @return bool
     */
    public function isCartButtonSkyboxEnabled()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * Returns if the Change Country is visibility (Countries are not allowed)
     *
     * @return bool
     */
    public function isChangeCountryEnabled()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * Returns value of the LocationAllow visibility
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->getLocationAllow() != 0 ? true : false;
    }

    /**
     * Cart Operations acction are available
     *
     * @return bool
     */
    public function isOperationCartEnabled()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        }

        return false;
    }

    /**
     * Hide or Show SubTotal on your cart details page
     * @return bool
     */
    public function showSubtotal()
    {
        if ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
            && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return false;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_SHOW
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() != self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        } elseif ($this->getIntegrationType() == self::TYPE_LOCATION_ALLOW_CART_HIDE
                  && $this->getLocationAllow() == self::TYPE_LOCATION_ALLOW_CART_DISABLE
        ) {
            return true;
        }

        return true;
    }
}
