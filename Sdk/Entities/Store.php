<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Store
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Store setId(string $value)
 * @method Store setKey(string $value)
 * @method Store setCode(string $value)
 * @method Store setLocationAllow(integer $value)
 * @method Store setIntegrationType(integer $value)
 * @method Store setShowLogin(bool $value)
 * @method Store setLanguageId(integer $value)
 * @method Store setUserTemporal(integer $value)
 * @method Store setCart(Cart $value)
 * @method Store setCountry(Country $value)
 * @method Store setCurrency(Currency $value)
 * @method Store setTemplate(string $value)
 * @method Store setCommodities(array $value)
 * @method Store setTag(array $value)
 * @method string getId()
 * @method string getKey()
 * @method string getCode()
 * @method integer getLocationAllow()
 * @method integer getIntegrationType()
 * @method bool getShowLogin()
 * @method integer getLanguageId()
 * @method integer getUserTemporal()
 * @method Cart getCart()
 * @method array getTag()
 * @method Country getCountry()
 * @method Currency getCurrency()
 * @method Template getTemplate()
 * @method array getCommodities()
 */

class Store extends AbstractEntity
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $code;

    /**
     * @var integer
     */
    public $locationAllow;

    /**
     * @var integer
     */
    public $integrationType;

    /**
     * @var integer
     */
    public $languageId;

    /**
     * @var bool
     */
    public $showLogin = false;

    /**
     * @var integer
     */
    public $userTemporal = 1;

    /**
     * @var Cart
     */
    public $cart;

    /**
     * @var Country;
     */
    public $country;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var Template
     */
    public $template;

    /**
     * @var array
     */
    public $commodities = [];

    /**
     * @var array
     */
    public $tag = [];

    /**
     * Store constructor.
     * @param $id
     * @param $key
     */
    public function __construct($id, $key)
    {
        $this->id = $id;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getNavBar()
    {
        return $this->template->getNavBar();
    }
}
