<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Plugin;

class ButtonPlugin
{
    private $configHelper;
    private $dataHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Skybox\Checkout\Helper\Config $configHelper
     * @param \Skybox\Checkout\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Response\Http $response,
        \Skybox\Checkout\Helper\Config $configHelper,
        \Skybox\Checkout\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->response              = $response;
        $this->dataHelper            = $dataHelper;
        $this->configHelper          = $configHelper;
        $this->_url                  = $context->getUrl();
        $this->logger                = $logger;
    }

    /**
     * Checkout page
     *
     * @param $subject
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function afterExecute($subject, $result)
    {
        $subjectVar = $subject;

        if (is_null($this->dataHelper->getClient())) {
            return $result;
        }
        if (!$this->configHelper->getEnabled()) {
            return $result;
        }
        if ($this->dataHelper->allowed()->isCartButtonEnabled()) {
            $url = $this->_url->getUrl('skbcheckout/international');
            $this->response->setRedirect($url)->sendResponse();
        }

        return $result;
    }
}
