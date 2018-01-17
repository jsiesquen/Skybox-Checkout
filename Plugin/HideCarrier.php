<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin;

use Magento\Backend\Model\Auth\Session;

class HideCarrier
{
    private $backendSession;

    /**
     * HideCarrier constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->backendSession = $session;
    }

    /**
     * @param $subject
     * @param callable $proceed
     *
     * @return bool
     */
    public function aroundCanCollectRates($subject, callable $proceed)
    {
        $subjectVar = $subject;
        if ($this->backendSession->isLoggedIn()) {
            return $proceed();
        }

        return false;
    }
}
