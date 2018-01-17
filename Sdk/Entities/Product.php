<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Product
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Product setSkyboxProductId(integer $value)
 * @method Product setHtmlObjectId(string $value)
 * @method Product setId(string $value)
 * @method Product setName(string $value)
 * @method Product setSku(string $value)
 * @method Product setPrice(mixed $value)
 * @method Product setQuantity(integer $value)
 * @method Product setLanguage(string $value)
 * @method Product setCategory(string $value)
 * @method Product setImg(string $value)
 * @method Product setWeight(float $value)
 * @method Product setWeightUnit(string $value)
 * @method Product setVolumetricWeight(float $value)
 * @method Product setAttributes(array $value)
 * @method string getSkyboxProductId()
 * @method string getHtmlObjectId()
 * @method string getId()
 * @method string getName()
 * @method string getSku()
 * @method mixed getPrice()
 * @method integer getQuantity()
 * @method float getAmount()
 * @method string getLanguage()
 * @method string getCategory()
 * @method string getImg()
 * @method float getWeight()
 * @method string getWeightUnit()
 * @method float getVolumetricWeight()
 */

class Product extends AbstractEntity
{
    /** @var integer */
    public $skyboxProductId;

    /** @var  string */
    public $htmlObjectId;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var double
     */
    public $price;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var double
     */
    public $amount;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $img;

    /**
     * @var integer
     */
    public $weight;

    /**
     * @var string
     */
    public $weightUnit;

    /**
     * @var integer
     */
    public $volumetricWeight;
}
