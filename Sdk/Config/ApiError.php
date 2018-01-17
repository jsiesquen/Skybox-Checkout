<?php

namespace Skybox\Checkout\Sdk\Config;

class ApiError
{
    const NOT_MAPPED_ERROR = 666;

    const GCODE_BADREQUEST = 400;

    const GCODE_SERVICE_SUCCESS = 10000;

    const GCODE_SERVICE_NOT_CONTROLLER = 50000;

    const GCODE_IDMERCHANT_INVALID = 50001;

    const GCODE_TOKEN_EXPIRED = 50002;

    const GCODE_CART_ID_INVALID = 50003;

    const GCODE_PRODUCT_NOT_IN_SHOPPING_CART = 50004;

    const GCODE_COUNTRY_NOT_CONFIGURED_TO_STORE = 50005;

    const GCODE_MERCHANT_KEY_INVALID = 50006;

    const GCODE_IP_NOT_ALLOWED_TO_STORE = 50007;

    const GCODE_PRODUCT_CATEGORY_INVALID = 50008;

    const GCODE_PRODUCT_UNITWEIGHT_IS_NULL = 50009;

    const GCODE_PRODUCT_WEIGHT_INVALID = 50010;

    const GCODE_PRODUCT_WEIGHT_IS_NULL = 50011;

    const GCODE_MERCHANT_KEY_IS_NULL = 50013;

    const GCODE_PRODUCT_UNITWEIGHT_INVALID = 50014;

    const GCODE_CITY_NOT_CONFIGURED_TO_STORE = 50015;

    const GCODE_TOKEN_IS_NULL = 50016;

    const GCODE_GUID_IS_NULL = 50017;

    const GCODE_PRODUCT_PRICE_INVALID = 50018;

    const GCODE_PRODUCT_NAME_IS_NULL = 50019;

    const GCODE_PRODUCT_CODE_IS_NULL = 50020;

    const GCODE_RATES_NOT_FOUND = 50021;

    const GCODE_CONCEPT_DYNAMIC_NOT_FOUND = 50022;

    const GCODE_TEMPLATE_BUTTON_ERROR = 50023;

    const GCODE_CUSTOMER_REQUIRED = 50024;

    const GCODE_CUSTOMER_IP_REQUIRED = 50025;

    const GCODE_CUSTOMER_BROWSER_REQUIRED = 50026;

    const GCODE_ADDCART_PRODUCT_REQUIRED = 50027;

    const GCODE_ADDTOCART_PRODUCT_SKU_REQUIRED = 50028;

    const GCODE_MERCHANT_REQUIRED = 50029;

    const GCODE_MERCHANT_ID_REQUIRED = 50030;

    const GCODE_REGION_NOT_CONFIGURED_TO_STORE = 50028;

    const GCODE_NOT_INPUT_MULTIPLECALCULATE = 50028;

    const GCODE_NOT_LISTPRODUCT_MULTIPLECALCULATE = 50028;

    private $messages = [
        self::NOT_MAPPED_ERROR => 'SkyBox Checkout API Error not mapped. Check it!',
        self::GCODE_BADREQUEST => 'Bad request',
        self::GCODE_SERVICE_SUCCESS => 'Service Success',
        self::GCODE_SERVICE_NOT_CONTROLLER => 'Error not handled',
        self::GCODE_IDMERCHANT_INVALID => 'Invalid Merchant Id',
        self::GCODE_TOKEN_EXPIRED => 'Token Expired',
        self::GCODE_PRODUCT_NOT_IN_SHOPPING_CART => 'The Product is not in the Shopping Cart',
        self::GCODE_COUNTRY_NOT_CONFIGURED_TO_STORE => 'GCODE_COUNTRY_NOT_CONFIGURED_TO_STORE',
        self::GCODE_MERCHANT_KEY_INVALID => 'Invalid Merchant Key',
        self::GCODE_IP_NOT_ALLOWED_TO_STORE => 'IP Not Allowed',
        self::GCODE_PRODUCT_CATEGORY_INVALID => 'The Commodity/Category is invalid',
        self::GCODE_PRODUCT_UNITWEIGHT_IS_NULL => 'Weight Unit is invalid',
        self::GCODE_PRODUCT_WEIGHT_INVALID => 'Product Weight is invalid',
        self::GCODE_PRODUCT_WEIGHT_IS_NULL => 'Product Weight is null and not valid',
        self::GCODE_MERCHANT_KEY_IS_NULL => 'Merchant Key is null or empty',
        self::GCODE_PRODUCT_UNITWEIGHT_INVALID => 'Product Unit Weight is not valid',
        self::GCODE_CITY_NOT_CONFIGURED_TO_STORE => 'The Store City is not configured',
        self::GCODE_TOKEN_IS_NULL => 'Token is null',
        self::GCODE_GUID_IS_NULL => 'Cart Id is null',
        self::GCODE_PRODUCT_PRICE_INVALID => 'The Product Price is empty or invalid',
        self::GCODE_PRODUCT_NAME_IS_NULL => 'The Product message is null',
        self::GCODE_PRODUCT_CODE_IS_NULL => 'The Product Code is null',
        self::GCODE_RATES_NOT_FOUND => 'The Store Rates are not configured correctly',
        self::GCODE_CONCEPT_DYNAMIC_NOT_FOUND => 'The Concept Dynamic is not exits or configured correctly',
        self::GCODE_TEMPLATE_BUTTON_ERROR => 'Error on Template Button',
        self::GCODE_CUSTOMER_REQUIRED => 'Customer Data Required',
        self::GCODE_CUSTOMER_IP_REQUIRED => 'Customer IP is required',
        self::GCODE_CUSTOMER_BROWSER_REQUIRED => 'Customer Browser is required',
        self::GCODE_ADDCART_PRODUCT_REQUIRED => 'The Product To Add To Cart is required',
        self::GCODE_ADDTOCART_PRODUCT_SKU_REQUIRED => 'The Product SKU To Add To Cart is required',
        self::GCODE_MERCHANT_REQUIRED => 'Merchant is required',
        self::GCODE_MERCHANT_ID_REQUIRED => 'The Merchant Id is required',
        self::GCODE_REGION_NOT_CONFIGURED_TO_STORE => 'Region is not configured on the Store',
        self::GCODE_NOT_INPUT_MULTIPLECALCULATE => 'Not input data to MultiCalculate is given',
        self::GCODE_NOT_LISTPRODUCT_MULTIPLECALCULATE => 'Not ListProduct data to MultiCalculate is given',
    ];

    /**
     * @param int $code
     *
     * @return array
     */
    public function getError($code = self::NOT_MAPPED_ERROR)
    {
        $error = $this->getDefaultError();
        if (isset($this->messages[$code])) {
            $error = [
                'code' => $code,
                'message' => $this->messages[$code]
            ];
        }

        return $error;
    }

    /**
     * @return array
     */
    public function getDefaultError()
    {
        return [
            'code' => self::NOT_MAPPED_ERROR,
            'message' => $this->messages[self::NOT_MAPPED_ERROR],
        ];
    }
}
