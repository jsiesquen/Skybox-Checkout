<?php
/**
 * Copyright © 2017 SkyBox Checkout Inc. All rights reserved.
 */

namespace Skybox\Checkout\Model\Data;

use Skybox\Checkout\Api\Data\CustomsInterface;

class Customs implements CustomsInterface
{
    public $param1;
    public $param2;
    public $param3;
    public $param4;
    public $param5;

    /**
     * @return mixed
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * @param mixed $param1
     */
    public function setParam1($param1)
    {
        $this->param1 = $param1;
    }

    /**
     * @return mixed
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * @param mixed $param2
     */
    public function setParam2($param2)
    {
        $this->param2 = $param2;
    }

    /**
     * @return mixed
     */
    public function getParam3()
    {
        return $this->param3;
    }

    /**
     * @param mixed $param3
     */
    public function setParam3($param3)
    {
        $this->param3 = $param3;
    }

    /**
     * @return mixed
     */
    public function getParam4()
    {
        return $this->param4;
    }

    /**
     * @param mixed $param4
     */
    public function setParam4($param4)
    {
        $this->param4 = $param4;
    }

    /**
     * @return mixed
     */
    public function getParam5()
    {
        return $this->param5;
    }

    /**
     * @param mixed $param5
     */
    public function setParam5($param5)
    {
        $this->param5 = $param5;
    }
}
