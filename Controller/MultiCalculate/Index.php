<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Controller\MultiCalculate;

use Magento\Framework\Exception\LocalizedException;

class Index extends \Magento\Framework\App\Action\Action
{
    private $resultPageFactory;
    private $resultJsonFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Product $catalogProduct
     * @param \Skybox\Checkout\Block\Product\AfterPrice $afterPrice
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Product $catalogProduct,
        \Skybox\Checkout\Block\Product\AfterPrice $afterPrice,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->catalogProduct    = $catalogProduct;
        $this->afterPrice        = $afterPrice;
        $this->logger            = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->logger->debug('[SBC] Controller\MultiCalculate\Index::execute');
        if ($this->getRequest()->isPost()) {
            try {
                $productId = $this->getRequest()->getPost('productId', null);
                $price     = $this->getRequest()->getPost('price', null);
                if (empty($productId)) {
                    throw new LocalizedException('Product Id is Empty');
                }
                $product     = $this->catalogProduct->load($productId);
                $dataProduct = $this->afterPrice->getDataProduct($product, $price);
                $htmlPrice   = $this->afterPrice->getBasePrice($dataProduct);

                $this->logger->debug("[SBC] Controller\MultiCalculate\Index::execute: dataProduct:" .
                                     json_encode($dataProduct));

                $result      = [
                    'status' => 1,
                    'msg'    => 'ok',
                    'result' => ['template' => $htmlPrice]
                ];
            } catch (\Exception $exception) {
                $this->logger->debug('[SBC] Controller\MultiCalculate\Index::execute (exception): ' .
                                     $exception->getMessage());
                $result = [
                    'status' => 0,
                    'msg'    => $exception->getMessage(),
                    'result' => 'none'
                ];
            }

            return $this->resultJson($result);
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
