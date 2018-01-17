<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Model\Data;

use Skybox\Checkout\Api\Data\TotalShoppingCartInterface;

class TotalShoppingCart implements TotalShoppingCartInterface
{
    public $totalPriceUSD;
    public $totalPriceCustomer;
    public $totalFeeUSD;
    public $totalFeeCustomer;
    public $listDetailConcepts;

    /**
     * @return mixed
     */
    public function getTotalPriceUSD()
    {
        return $this->totalPriceUSD;
    }

    /**
     * @param mixed $totalPriceUSD
     */
    public function setTotalPriceUSD($totalPriceUSD)
    {
        $this->totalPriceUSD = $totalPriceUSD;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceCustomer()
    {
        return $this->totalPriceCustomer;
    }

    /**
     * @param mixed $totalPriceCustomer
     */
    public function setTotalPriceCustomer($totalPriceCustomer)
    {
        $this->totalPriceCustomer = $totalPriceCustomer;
    }

    public function getTotalFeeUSD()
    {
        return $this->totalFeeUSD;
    }

    public function setTotalFeeUSD($totalFeeUSD)
    {
        $this->totalFeeUSD = $totalFeeUSD;
    }

    public function getTotalFeeCustomer()
    {
        return $this->totalFeeCustomer;
    }

    public function setTotalFeeCustomer($totalFeeCustomer)
    {
        $this->totalFeeCustomer = $totalFeeCustomer;
    }

    /**
     * @return \Skybox\Checkout\Api\Data\ConceptInterface[]
     */
    public function getListDetailConcepts()
    {
        return $this->listDetailConcepts;
    }

    /**
     * @param \Skybox\Checkout\Api\Data\ConceptInterface[] $listDetailConcepts
     */
    public function setListDetailConcepts($listDetailConcepts)
    {
        $this->listDetailConcepts = $listDetailConcepts;
    }
}
