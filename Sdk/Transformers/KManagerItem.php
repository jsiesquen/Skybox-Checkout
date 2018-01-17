<?php

namespace Skybox\Checkout\Sdk\Transformers;

/***
 * Class KManagerItem
 * @package Skybox\Checkout\Sdk\Transformers
 */

class KManagerItem
{
    /**
     * @var mixed
     */
    public $item;

    /**
     * KManagerItem constructor.
     *
     * @param $obj
     * @param KTransformer $kTransformer
     */
    public function __construct($obj, KTransformer $kTransformer)/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        $this->item = $kTransformer->transform((object) $obj);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->item;
    }
}
