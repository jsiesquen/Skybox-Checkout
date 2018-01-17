<?php

namespace Skybox\Checkout\Sdk\Services;

use Skybox\Checkout\Sdk\Entities\Cart;
use Skybox\Checkout\Sdk\Entities\CartDetail;
use Skybox\Checkout\Sdk\Entities\Product;
use Skybox\Checkout\Sdk\Entities\ProductCalculate;
use Skybox\Checkout\Sdk\Exceptions\SkyboxNotAcceptableException;
use Skybox\Checkout\Sdk\Exceptions\SkyboxUnauthorizedException;
use Skybox\Checkout\Sdk\Transformers\CartDetailTransformer;
use Skybox\Checkout\Sdk\Transformers\CartTransformer;
use Skybox\Checkout\Sdk\Transformers\CountryTransformer;
use Skybox\Checkout\Sdk\Transformers\CurrencyTransformer;
use Skybox\Checkout\Sdk\Transformers\ProductCalculateTransformer;
use Skybox\Checkout\Sdk\Transformers\ProductSkyboxTransformer;
use Skybox\Checkout\Sdk\Transformers\TemplateTransformer;
use Skybox\Checkout\Sdk\Entities\CustomerDevice;
use Skybox\Checkout\Sdk\Entities\Store;
use Skybox\Checkout\Sdk\Entities\Token;
use Skybox\Checkout\Sdk\Transformers\CustomerDeviceSkyboxTransformer;
use Skybox\Checkout\Sdk\Transformers\KManagerItem;
use stdClass;

/**
 * Class SkyboxCartService
 * @package Skybox\Checkout\Sdk\Services
 */
class SkyboxCartService extends SkyboxService
{
    const STANDARD = 0;
    const CART_ONLY_VIEW = 1;

    /**
     * @param CustomerDevice $customerDevice
     *
     * @return Store
     */
    public function create(CustomerDevice $customerDevice)
    {
        $customerDeviceSkybox = new KManagerItem($customerDevice, new CustomerDeviceSkyboxTransformer()); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

        $params = [
            'MerchantId' => $this->store->getId(),
            'CartId'     => null,
            'Customer'   => $customerDeviceSkybox->get(),
        ];

        $httpResponse = $this->adapter->post('cart', $params, $this->getHeaders());

        $response = $this->adapter->parseResponse($httpResponse);

        $this->registerStoreSettings($this->store, $response);
        $this->registerCurrency($this->store, $response);
        $this->registerCart($this->store, $response);
        $this->registerCountry($this->store, $response);
        $this->registerTemplate($this->store, $response);

        return $this->store;
    }

    /**
     * @param Product $product
     *
     * @return ProductCalculate
     * @throws SkyboxNotAcceptableException
     */
    public function addItem(Product $product)
    {
        $productCalculate = null;
        try {
            $skyboxProduct = new KManagerItem($product, new ProductSkyboxTransformer()); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */

            $cartId = $this->store->getCart()->getId();

            $params = [
                'Product' => $skyboxProduct->get()
            ];

            $httpResponse = $this->adapter->post("cart/{$cartId}/product", $params, $this->getCartHeaders());
            $response     = $this->adapter->parseResponse($httpResponse);

            $proT = new ProductCalculateTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            $productCalculateResponse = new KManagerItem($response, $proT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            /** @var ProductCalculate $productCalculate */
            $productCalculate = $productCalculateResponse->get();
            $product->setSkyboxProductId($productCalculate->getId());
            $this->store->getCart()->addItem($product);
        } catch(SkyboxNotAcceptableException $exception) {
            throw $exception;
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();
            return call_user_func_array([$this, 'addItem'], [$product]); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $productCalculate;
    }

    /**
     * @param Product|integer $product
     * @param int $quantity
     * @param float @prc
     *
     * @return mixed
     */
    public function updateItem($product, $quantity = null, $price = null)
    {
        $httpResponse = null;
        try {
            $cartId = $this->store->getCart()->getId();

            if (is_object($product)) {
                $productId          = $product->getSkyboxProductId();
                $productQuantity    = $product->getQuantity();
                $productPrice       = $product->getPrice();
            } elseif (is_int($product)) {
                $productId          = $product;
                $productQuantity    = $quantity;
                $productPrice       = $price;
            } else {
                $productId          = (int)($product);
                $productQuantity    = $quantity;
                $productPrice       = $price;
            }

            $params = [
                'Product' => [
                    'Quantity' => $productQuantity,
                    'Price' => $productPrice
                ]
            ];

            $httpResponse = $this->adapter->put("cart/{$cartId}/product/{$productId}/", $params, $this->getHeaders());

            $this->store->getCart()->updateItem($productId, $productQuantity);
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {

            $this->refreshToken();
            return call_user_func_array([$this, 'updateItem'], [$productId, $productQuantity]); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $this->adapter->parseResponse($httpResponse);
    }

    /**
     * @param Product|integer $product The Skybox Product Id
     *
     * @return mixed
     */
    public function removeItem($product)
    {
        $httpResponse = null;
        try {
            $cartId = $this->store->getCart()->getId();

            if (is_object($product)) {
                $productId = $product->getSkyboxProductId();
            } elseif (is_int($product)) {
                $productId = $product;
            } else {
                $productId = (int)($product);
            }

            $httpResponse = $this->adapter->delete("cart/{$cartId}/product/{$productId}", $this->getHeaders());

            $this->store->getCart()->removeItem($productId);
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();

            return call_user_func_array([$this, 'removeItem'], [$product]); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $this->adapter->parseResponse($httpResponse);
    }

    /**
     * Remove All Items
     * @return mixed
     */
    public function removeAll()
    {
        $httpResponse = null;
        try {
            $cartId = $this->store->getCart()->getId();

            $httpResponse = $this->adapter->delete("cart/{$cartId}/products", $this->getHeaders());

            $this->store->getCart()->removeAll();
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();

            return call_user_func_array(array($this, 'removeAll'), []); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $this->adapter->parseResponse($httpResponse);
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->store->getCart()->getItems();
    }

    /**
     * @return integer
     */
    public function getTotalItems()
    {
        return $this->store->getCart()->getTotalItems();
    }

    /**
     * @return CartDetail
     */
    public function getConcepts()
    {
        $cartDetail = null;
        try {
            $cartId = $this->store->getCart()->getId();

            $httpResponse = $this->adapter->get("cart/{$cartId}/concepts", $this->getHeaders());
            $response     = $this->adapter->parseResponse($httpResponse);

            $transformer = new KManagerItem($response, new CartDetailTransformer()); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            /** @var \Skybox\Checkout\Sdk\Entities\CartDetail $cartDetail */
            $cartDetail = $transformer->get();
            $this->store->getCart()->setDomesticCharge($cartDetail->getDomesticCharge());
            $this->store->getCart()->setInternationalCharge($cartDetail->getInternationalCharge());
            $this->store->getCart()->setConcepts($cartDetail->getConcepts());
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();
            return call_user_func_array([$this, 'getConcepts'], []); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $cartDetail;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->store->getCart()->getInternationalCharge()->getPrice();
    }

    /**
     * @return float
     */
    public function getGrandTotal()
    {
        if ($this->store->getCart()->getInternationalCharge() != null) {
            return $this->store->getCart()->getInternationalCharge()->getTotal();
        }

        return 0.0;
    }

    /**
     *
     */
    public function getInfo()
    {
        try {
            $cartId = $this->store->getCart()->getId();

            $httpResponse = $this->adapter->get("cart/{$cartId}", $this->getHeaders());
            $response     = $this->adapter->parseResponse($httpResponse);

            $this->registerStoreSettings($this->store, $response);
            $this->registerCurrency($this->store, $response);
            $this->registerCountry($this->store, $response);
            $this->registerTemplate($this->store, $response);
        } catch (SkyboxUnauthorizedException $skyboxUnauthorizedException) {
            $this->refreshToken();
            call_user_func_array([$this, 'getInfo'], []); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        }

        return $this;
    }

    /**
     * @return Cart
     */
    public function getEntity()
    {
        return $this->store->getCart();
    }

    /**
     * @param Store $store
     * @param stdClass $response
     */
    private function registerStoreSettings(Store $store, stdClass $response)
    {
        $store->setLocationAllow((int)$response->LocationAllow);
        $store->setIntegrationType($response->IntegrationType);
        $store->setCode(trim($response->StoreCode));
        $store->setLanguageId((int)($response->LanguageId));
    }

    /**
     * @param Store $store
     * @param stdClass $response
     */
    private function registerCurrency(Store $store, stdClass $response)
    {
        $currT = new CurrencyTransformer();/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $currency = new KManagerItem($response->Cart, $currT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $store->setCurrency($currency->get());
    }

    /**
     * @param Store $store
     * @param stdClass $response
     */
    private function registerCart(Store $store, stdClass $response)
    {
        $carT = new CartTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $cart = new KManagerItem($response->Cart, $carT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $store->setCart($cart->get());
    }

    /**
     * @param Store $store
     * @param stdClass $response
     */
    private function registerCountry(Store $store, stdClass $response)
    {
        $conT =  new CountryTransformer(); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $country = new KManagerItem($response->Country,$conT); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $store->setCountry($country->get());
    }

    /**
     * @param Store $store
     * @param stdClass $response
     */
    private function registerTemplate(Store $store, stdClass $response)
    {
        $tempT = new TemplateTransformer($response);/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $template = new KManagerItem($response->Template,$tempT ); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
        $store->setTemplate($template->get());
    }

    /*
     * Returns the correct Integration Type (trick)
     * @return integer
     */
    private function getIntegrationType($value)
    {
        $integrationType = 1; /** self::STANDARD */

        if (self::CART_ONLY_VIEW == $value) {
            $integrationType = 3;
        }

        return $integrationType;
    }
}
