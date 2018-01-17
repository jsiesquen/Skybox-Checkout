<?php

namespace Skybox\Checkout\Sdk\Entities;

/***
 * Class Template
 * @package Skybox\Checkout\Sdk\Entities
 *
 * @method Template setNavBar(string $navBar)
 * @method string getNavBar()
 * @method Template setVersion(integer $version)
 * @method integer getVersion()
 */

class Template extends AbstractEntity
{

    /**
     * @var string
     */
    public $navBar;

    /**
     * @var integer
     */
    public $version;
}
