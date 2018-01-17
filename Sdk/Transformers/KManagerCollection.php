<?php

namespace Skybox\Checkout\Sdk\Transformers;

/**
 * Class KManagerCollection
 * @package Skybox\Checkout\Sdk\Transformers
 */

class KManagerCollection
{
    public $data;

    /**
     * KManagerCollection constructor.
     *
     * @param array $collection
     * @param KTransformer $kTransformer
     */
    public function __construct(array $collection, KTransformer $kTransformer)/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        foreach ($collection as $item) {
            $newItem = new KManagerItem($item, $kTransformer); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            if ($newItem != null) {
                $this->data[] = $newItem->get();
            }
        }
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->data;
    }
}
