<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Controller\International;

class Index extends \Magento\Framework\App\Action\Action
{
    private $resultPageFactory;
    private $helper;
    private $interfaceObjectManager;
    private $urlHelper;
    private $view;
    public $assetRepository;
    private $checkoutSession;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Skybox\Checkout\Helper\Product\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Skybox\Checkout\Helper\Product\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->helper            = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->assetRepository   = $assetRepository;
        $this->checkoutSession   = $checkoutSession;
        $this->logger            = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->logger->debug('[SBC] Controller\International\Index::execute');

        $cart       = $this->objectManager('\Magento\Checkout\Model\Cart');
        $quote      = $cart->getQuote();
        $items      = $quote->getAllItems();
        $client     = $this->helper->getClient();
        $skyboxCart = $client->getCart();

        if (!empty($items) && !is_null($client)) {
            $checkoutLoaded = $this->checkoutSession->getSkyboxCheckoutLoaded();
            if (is_null($checkoutLoaded) || ($checkoutLoaded === 0))
            {
                $this->checkoutSession->setSkyboxCheckoutLoaded(1);
            }

            $config         = $this->helper->getConfig();
            $merchant       = $config->getMerchantId();
            $storeId        = $config->getStoreId();
            $cartId         = $quote->getId();
            $token          = $client->getToken()->getHash();
            $guid           = $client->getEntity()->getCart()->getId();
            $urlSuccess     = $this->getFrontendUrl('skbcheckout/international/success');
            $urlConfirm     = $this->getFrontendUrl('skbcheckout/international');

            $productNumber              = intval($skyboxCart->getTotalItems());
            $heightPerProductsNumber    = (($productNumber + 1) * 60);
            $conceptsNumber             = count($skyboxCart->getConcepts()->concepts);
            $heightPerConceptsNumber    = $conceptsNumber * 74;

            $url_checkout_page  = $config->getUrlClient() . "WebForms/Checkout/APICheckout.aspx";
            $url_checkout_format= "%s?token=%s&GuiId=%s&merchant=%s&idCart=%s&idStore=%s&UrlC=%s&paypal=%s&checkout=%s&UrlR=%s&%s";
            $url_checkout       = sprintf($url_checkout_format, $url_checkout_page, $token, $guid, $merchant, $cartId,
                                            $storeId, $urlConfirm,
                                            $this->filterParam($this->getRequest()->getParam('paypal', '')),
                                            $this->filterParam($this->getRequest()->getParam('checkout', '')),
                                            $urlSuccess,
                                            $client->getCart()->getEntity()->getDataUrl()
                                            );

            $jsAsset        = $this->assetRepository->getUrl('Skybox_Checkout::js/cart/international.js');
            $resetJsAsset   = $this->assetRepository->getUrl('Skybox_Checkout::js/cart/reset.js');

            $resultPage     = $this->resultPageFactory->create();
            $block          = $this->getView()->getLayout()->getBlock('skb.international.index');
            $block->assign([
                'height'       => 2050 + $heightPerProductsNumber + $heightPerConceptsNumber,
                'url'          => $url_checkout,
                'jsAsset'      => $jsAsset,
                'resetJsAsset' => $resetJsAsset
            ]);
        } else {
            $resultPage = $this->resultRedirectFactory->create();
            $resultPage->setPath('checkout/cart');
        }

        return $resultPage;
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
     * @param string $param
     *
     * @return string
     */
    private function filterParam($param)
    {
        return strip_tags($param);
    }

    /**
     * @param $routePath
     * @param array $routeParams
     *
     * @return mixed
     */
    private function getFrontendUrl($routePath, $routeParams = [])
    {
        if (empty($this->urlHelper)) {
            $this->urlHelper = $this->objectManager('\Magento\Framework\Url');
        }

        return $this->urlHelper->getUrl($routePath, $routeParams);
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
