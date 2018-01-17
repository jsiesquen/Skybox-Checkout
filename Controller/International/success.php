<?php
/**
 * Copyright © 2017 SkyBOX Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Controller\International;

class Success extends \Magento\Framework\App\Action\Action
{
    private $resultPageFactory;
    private $helper;
    private $interfaceObjectManager;
    private $urlHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * Success constructor.
     *
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Url $urlHelper
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Skybox\Checkout\Helper\Product\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Url $urlHelper,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->helper            = $helper;
        $this->urlHelper         = $urlHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->checkoutSession   = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Checkout\Model\Cart $cartObject */
        $cartObject = $this->objectManager('\Magento\Checkout\Model\Cart');
        $items      = $cartObject->getQuote()->getAllItems();
        $client     = $this->helper->getClient();

        if (!empty($items) && !is_null($client)) {
            $this->checkoutSession->setUpdateLocalStorage(1);
            $resultPage     = $this->resultPageFactory->create();
            $block          = $this->getView()->getLayout()->getBlock('skb.international.success');

            $block->assign([
                'messageSuccess' => __('Thanks for your purchase, We have received your order and we will contact you. We will send you a confirmation email.'),
                'invoiceHtml'    => $this->getRequest()->getPost('INVOICE_HTML', ''),
                'tagCartOrdered' => $this->getRequest()->getPost('TAG_CART_ORDERED', '')
            ]);
            $cartObject->getQuote()->removeAllItems();
            $cartObject->saveQuote();
            $this->logger->debug("[getNavBar-1]" . $client->getTemplate()->getNavBar());
            $this->checkoutSession->setNavigationBar($client->getTemplate()->getNavBar());
            $this->helper->getDataHelper()->cleanAuth($client);
            $this->checkoutSession->unsStoreObject();
            $this->logger->debug("[getNavBar-2]" . $client->getTemplate()->getNavBar());
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('checkout/cart');

            return $resultRedirect;
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    private function objectManager($name)
    {
        if (empty($interfaceObjectManager)) {
            $this->interfaceObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }

        return $this->interfaceObjectManager->get($name);
    }

    /**
     * @return mixed
     */
    private function getView()
    {
        if (empty($this->view)) {
            $this->view = $this->objectManager('\Magento\Backend\Model\View\Result\Page');
        }

        return $this->view;
    }
}
