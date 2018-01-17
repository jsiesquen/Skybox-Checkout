<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Model\Data;

use Skybox\Checkout\Api\Data\ConceptInterface;

class Concept implements ConceptInterface
{
    public $concept;
    public $value;
    public $valueUSD;
    public $currency;
    public $visible;

    /**
     * @return mixed
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @param mixed $concept
     */
    public function setConcept($concept)
    {
        $this->concept = $concept;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValueUSD()
    {
        return $this->valueUSD;
    }

    /**
     * @param mixed $valueUSD
     */
    public function setValueUSD($valueUSD)
    {
        $this->valueUSD = $valueUSD;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $currency       = trim($currency);
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $data = [
            "Concept"  => $this->getConcept(),
            "Value"    => $this->getValue(),
            "ValueUSD" => $this->getValueUSD(),
            "Currency" => $this->getCurrency(),
            "Visible"  => $this->getValue(),
        ];

        return $data;
    }
}
