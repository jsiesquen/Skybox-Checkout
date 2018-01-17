<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Service;

use Skybox\Checkout\Api\StatusOrderInterface;

class StatusOrder implements StatusOrderInterface
{
    private $order;

    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->order  = $order;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function update($orderIncrementId, $status) {
        $this->logger->debug("[SBC] StatusOrder::update: Go into the flow - StatusOrder::update");
        $order = $this->order->loadByIncrementId($orderIncrementId);

        // State & Status
        $order->setState($status)->setStatus($status);

        // Total Paid
        $order->setTotalPaid($order->getGrandTotal());

        if (\Magento\Sales\Model\Order::STATE_COMPLETE == $status) {
            $history = $order->addStatusHistoryComment('[SBC] StatusOrder::update: The Order was marked as completed.', false);
            $history->setIsCustomerNotified(false);
        }
        $order->save();

        return 1;
    }
}
