<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Skybox\Checkout\Logger\Handler;

use Monolog\Logger;

/**
 * SkyBox Checkout Logger Handler
 *
 * @package Skybox\Checkout\Logger\Handler
 */
class System extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     *
     * @var int
     */
    public $loggerType = Logger::DEBUG;

    /**
     * File name
     *
     * @var string
     */
    public $fileName = '/var/log/skyboxcheckout.log';
}
