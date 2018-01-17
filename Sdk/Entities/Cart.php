<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Cart
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Cart setId(string $value)
 * @method Cart setTotalItems(integer $value)
 * @method Cart setDataUrl(string $value)
 * @method Cart setDomesticCharge(CartCharge $value)
 * @method Cart setInternationalCharge(CartCharge $value)
 * @method Cart setConcepts(array $value)
 * @method Cart setItems(array $value)
 * @method string getId()
 * @method string getTotalItems()
 * @method array getConcepts()
 * @method string getDataUrl()
 * @method CartCharge getDomesticCharge()
 * @method CartCharge getInternationalCharge()
 * @method array getItems()
 */

class Cart extends AbstractEntity
{
    const PRODUCT_ID = 'skyboxProductId';

    /**
     * @var string
     */
    public $id;

    /**
     * @var int
     */
    public $totalItems = 0;

    /**
     * @var string
     */
    public $dataUrl;

    /**
     * @var CartCharge
     */
    public $domesticCharge;

    /**
     * @var CartCharge
     */
    public $internationalCharge;

    /**
     * @var array
     */
    public $concepts = [];

    /**
     * @var array
     */
    public $items = [];

    /**
     * @param Product $product
     */
    public function addItem(Product $product)
    {
        $productId = $product->getSkyboxProductId();
        $this->items[$productId] = $product;
        $this->_countItems();
    }

    /**
     * @param integer $productId
     * @param integer $quantity
     *
     * @return bool
     */
    public function updateItem($productId, $quantity)
    {
        if (isset($this->items[$productId])) {
            /** @var Product $product */
            $product = $this->items[$productId];
            $product->setQuantity($quantity);
            $this->items[$productId] = $product;

            $this->_countItems();

            return true;
        }

        return false;
    }

    /**
     * @param integer $productId
     *
     * @return bool
     */
    public function removeItem($productId)
    {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);

            $this->_countItems();

            return true;
        }

        return false;
    }

    /**
     * Remove all items
     */
    public function removeAll()
    {
        $this->items = [];
        $this->_countItems();
    }

    /**
     *
     */
    private function _countItems()
    {
        $this->totalItems = 0;
        /** @var Product $product */
        foreach ($this->items as $key => $product) {
            $this->totalItems = $this->totalItems + $product->getQuantity();
        }
    }
}
