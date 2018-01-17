<?php

namespace Skybox\Checkout\Sdk\Transformers;

/***
 * Class KManagerMap
 * @package Skybox\Checkout\Sdk\Transformers
 */

class KManagerMap
{
    public $data;

    /**
     * KManagerMap constructor.
     *
     * @param array $collection
     * @param KTransformer $kTransformer
     */
    public function __construct(array $collection, KTransformer $kTransformer)/** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
    {
        foreach ($collection as $item) {
            $newItem = new KManagerItem($item, $kTransformer); /** @codingStandardsIgnoreLine MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation */
            if ($newItem != null && is_array($newItem->get())) {
                $id              = key($newItem->get());
                $value           = current($newItem->get());
                $this->data[$id] = $value;
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
