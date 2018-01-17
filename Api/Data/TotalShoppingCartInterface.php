<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Api\Data;

/**
 * TotalShoppingCart Interface
 * @package Skybox\Checkout\Api\Data
 */
interface TotalShoppingCartInterface
{
    /**
     * @return mixed
     */
    public function getTotalPriceUSD();

    /**
     * @param mixed $totalPriceUSD
     */
    public function setTotalPriceUSD($totalPriceUSD);

    /**
     * @return mixed
     */
    public function getTotalPriceCustomer();

    /**
     * @param mixed $totalPriceCustomer
     */
    public function setTotalPriceCustomer($totalPriceCustomer);

    /**
     * @return mixed
     */
    public function getTotalFeeUSD();

    /**
     * @param mixed $totalFeeUSD
     */
    public function setTotalFeeUSD($totalFeeUSD);

    /**
     * @return mixed
     */
    public function getTotalFeeCustomer();

    /**
     * @param mixed $totalFeeCustomer
     */
    public function setTotalFeeCustomer($totalFeeCustomer);

    /**
     * @return \Skybox\Checkout\Api\Data\ConceptInterface[]
     */
    public function getListDetailConcepts();

    /**
     * @param \Skybox\Checkout\Api\Data\ConceptInterface[] $listDetailConcepts
     */
    public function setListDetailConcepts($listDetailConcepts);
}
