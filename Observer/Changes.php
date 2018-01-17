<?php
/**
 * Created by PhpStorm.
 * User: SKYNET3
 * Date: 17/01/2018
 * Time: 12:32
 */

namespace Skybox\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Changes implements ObserverInterface
{
    public function __construct(
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $session
    )
    {
        $this->configHelper = $configHelper;
        $this->dataHelper   = $dataHelper;
        $this->session      = $session;
        $this->logger       = $logger;
        $this->client       = $this->dataHelper->getClient();

        $this->session->setData('multicalculate_response', array());
    }
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer) {
        $collection         = $observer->getEvent()->getData('collection');
        var_dump($collection);
    }
}
