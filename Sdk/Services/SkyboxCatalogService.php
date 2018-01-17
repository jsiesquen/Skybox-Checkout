<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Entities\Product;
use Skybox\Checkout\Sdk\Entities\ProductCalculate;
use Skybox\Checkout\Sdk\Exceptions\SkyBoxErrorException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxUnauthorizedException;
use Skybox\Checkout\Sdk\Transformers\CommodityTransformer;
use Skybox\Checkout\Sdk\Transformers\KManagerCollection;
use Skybox\Checkout\Sdk\Transformers\KManagerItem;
use Skybox\Checkout\Sdk\Transformers\KManagerMap;
use Skybox\Checkout\Sdk\Transformers\ProductCalculateTransformer;
use Skybox\Checkout\Sdk\Transformers\ProductMultiCalculateTransformer;
use Skybox\Checkout\Sdk\Transformers\ProductSkyboxTransformer;
use Skybox\Checkout\Sdk\Transformers\TagTransformer;

/**
 * Class SkyboxCatalogService
 * @package Skybox\Checkout\Sdk\Services
 */
class SkyboxCatalogService extends SkyboxService
{

    /**
     * @param Product $product
     * @param int $quantity
     * @param bool $mustLog
     *
     * @return ProductCalculate
     * @throws SkyBoxErrorException
     * @internal param int $quantity
     */
    public function calculate(Product $product, $quantity = 1, $mustLog = false)
    {
        $productCalculate = null;
        try {
            $sbcPro = new ProductSkyboxTransformer();
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $sbcProT = new KManagerItem($product, $sbcPro);
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $skyboxProduct = $sbcProT;
            $params = [
                'Product' => $skyboxProduct->get()
            ];

            $httpResponse = $this->adapter->post('calculate', $params, $this->getCartHeaders());
            $response = $this->adapter->parseResponse($httpResponse, $mustLog);

            $proC = new ProductCalculateTransformer();
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $productCalculate = new KManagerItem($response, $proC);
            /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();
            return call_user_func_array([$this, 'calculate'], [$product, $quantity]);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        } catch (SkyBoxErrorException $skyboxErrorException) {
            throw new SkyBoxErrorException($skyboxErrorException);
        }

        return $productCalculate->get();
    }

    /**
     * @param array $products
     *
     * @return mixed
     */
    public function multiCalculate(array $products)
    {
        $htmlProducts = [];
        try {
            $sbcPro         = new ProductSkyboxTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $skyboxProducts = new KManagerCollection($products, $sbcPro);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $params         = [
                                'ListProducts' => $skyboxProducts->get()
                                ];
            $httpResponse   = $this->adapter->post('multicalculate', $params, $this->getCartHeaders());
            $response       = $this->adapter->parseResponse($httpResponse);
            if (!empty($response)) {
                $resultT        = new ProductMultiCalculateTransformer();   /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $result         = new KManagerMap($response, $resultT);     /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $htmlProducts   = $result->get();
            }
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();
            return call_user_func_array([$this, 'multiCalculate'], [$products]);    /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $htmlProducts;
    }

    /**
     * @return mixed
     */
    public function commodities()
    {
        $commodities = $this->store->getCommodities();
        try {
            if (empty($commodities)) {
                $httpResponse = $this->adapter->get('commodities', $this->getHeaders());
                $response     = $this->adapter->parseResponse($httpResponse);

                $listT = new CommodityTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $list  = new KManagerCollection($response->Commodities,$listT );/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
                $commodities = $list->get();
                $this->store->setCommodities($commodities);
            }
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();

            return call_user_func_array([$this, 'commodities'], []);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        } catch (SkyBoxErrorException $exception) {
            $commodities = [];
        }

        return $commodities;
    }

    /**
     * @return mixed
     */
    public function storestyle()
    {
        $tag = null;
        try {
            $httpResponse = $this->adapter->get("store/styles", $this->getHeaders());

            $response = $this->adapter->parseResponse($httpResponse);

            $tagT = new TagTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $tag = new KManagerItem($response->Tags->Cart, $tagT);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();

            return call_user_func_array([$this, 'storestyle'], []);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        } catch (SkyBoxErrorException $exception) {
            $tag = [];
        }

        return $tag->get();
    }
}
