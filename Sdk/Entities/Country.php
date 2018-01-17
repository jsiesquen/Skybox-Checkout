<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Country
 * @package Skybox\Checkout\Sdk\Entities

 * @method Country setIso(string $value)
 * @method Country setName(string $value)
 * @method Country setRegion(Region $value)
 * @method Country setCity(City $value)
 * @method Country setFlag(string $value)
 * @method string getIso()
 * @method string getName()
 * @method Region getRegion()
 * @method City getCity()
 * @method string getFlag()
 */

class Country extends AbstractEntity
{

    /**
     * @var string
     */
    public $iso;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Region
     */
    public $region;

    /**
     * @var City
     */
    public $city;

    /**
     * @var string
     */
    public $flag;
}
