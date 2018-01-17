<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Controller\Process;

use Magento\Checkout\Model\Session\Proxy;

class Reset extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Skybox\Checkout\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Skybox\Checkout\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Skybox\Checkout\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper            = $helper;
        $this->logger            = $logger;
        $this->checkoutSession   = $checkoutSession;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $client = $this->helper->getClient();
            if (is_null($client)) {
                return $this->resultJson([  'status' => 0,
                                            'message' => 'Error trying to access the Skybox Checkout service'
                ]);
            }

            $this->checkoutSession->setUpdateLocalStorage(1);

            $this->helper->cleanAuth($client);

            return $this->resultJson([
                'status'    => 1,
                'message'   => 'OK',
            ]);
        }
    }

    /**
     * @param $data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function resultJson($data)
    {
        $result = $this->resultJsonFactory->create();
        $result->setHeader('Content-type', 'aplication/json; charset=UTF-8');
        $result->setHttpResponseCode(\Magento\Framework\Webapi\Response::HTTP_OK);
        $result->setData($data);

        return $result;
    }
}
