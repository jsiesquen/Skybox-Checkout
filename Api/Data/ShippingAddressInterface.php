<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Api\Data;

/**
 * Shipping Address Interface
 * @package Skybox\Checkout\Api\Data
 */
interface ShippingAddressInterface
{
    /**
     * @return mixed
     */
    public function getFirstname();

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname);

    /**
     * @return mixed
     */
    public function getLastname();

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname);

    /**
     * @return mixed
     */
    public function getStreet();

    /**
     * @param mixed $street
     */
    public function setStreet($street);

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @param mixed $city
     */
    public function setCity($city);

    /**
     * @return mixed
     */
    public function getCountryId();

    /**
     * @param mixed $country_id
     */
    public function setCountryId($country_id);

    /**
     * @return mixed
     */
    public function getRegion();

    /**
     * @param mixed $region
     */
    public function setRegion($region);

    /**
     * @return mixed
     */
    public function getPostcode();

    /**
     * @param mixed $postcode
     */
    public function setPostcode($postcode);

    /**
     * @return mixed
     */
    public function getTelephone();

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone);

    /**
     * @return mixed
     */
    public function toArray();
}
