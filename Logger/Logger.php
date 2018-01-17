<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Logger;

/**
 * SkyBox Checkout Custom Logger
 *
 * @package Skybox\Checkout\Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @var string
     */
    public $name = 'skyboxcheckout';

    /**
     * Set logger name
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
