<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Api\Data;

/**
 * Concept Interface
 * @package Skybox\Checkout\Api\Data
 */
interface ConceptInterface
{
    /**
     * @return mixed
     */
    public function getConcept();

    /**
     * @param mixed $concept
     */
    public function setConcept($concept);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValueUSD();

    /**
     * @param mixed $valueUSD
     */
    public function setValueUSD($valueUSD);

    /**
     * @return mixed
     */
    public function getCurrency();

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency);

    /**
     * @return mixed
     */
    public function getVisible();

    /**
     * @param mixed $visible
     */
    public function setVisible($visible);

    /**
     * @return mixed
     */
    public function toArray();
}
