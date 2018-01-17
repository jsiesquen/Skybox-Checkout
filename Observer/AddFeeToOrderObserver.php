<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Constructor.
     *
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Set SkyBox fee to order
     *
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote  = $observer->getQuote();
        $amount = $quote->getSkyboxFee();

        if (!$amount) {
            return $this;
        }

        // Set fee data to order
        $order  = $observer->getOrder();
        $amount = round($amount, 2);
        $order->setData('skybox_fee', $amount);

        return $this;
    }
}
