<?php
/**
 *  __  __     ______     ______     __     ______
 * /\ \/ /    /\  ___\   /\  __ \   /\ \   /\  == \
 * \ \  _"-.  \ \  __\   \ \ \/\ \  \ \ \  \ \  __<
 *  \ \_\ \_\  \ \_____\  \ \_____\  \ \_\  \ \_\ \_\
 *   \/_/\/_/   \/_____/   \/_____/   \/_/   \/_/ /_/
 *
 * Copyright (c) 2017. Developed by Zackary Pedersen, all rights reserved.
 * zackary@snaju.com - keoir.com - @keoir
 */

namespace Utils;

/**
 * Class Methods
 * @package Utils
 */
class Methods
{

    /**
     * @var
     */
    private static $methods;

    /**
     * @param Method $m
     */
    static function register(Method $m)
    {
        self::$methods[$m->getName()] = $m;
    }

    /**
     * @return mixed
     */
    static function getAll()
    {
        return self::$methods;
    }

    /**
     * @param $methodName
     * @return mixed
     */
    static function get($methodName)
    {
        return self::$methods[$methodName];
    }

    /**
     * @param $name
     * @return bool
     */
    static function existing($name)
    {
        if (isset(self::$methods[$name])) {
            return true;
        }

        return false;
    }
}

new Methods();

/**
 * Class Method
 * @package Utils
 */
class Method
{
    /**
     * @var string
     */
    private $name = "";
    /**
     * @var string
     */
    private $method = "get,post";
    /**
     * @var string
     */
    private $map = "/{name}/part";
    /**
     * @var array
     */
    private $otherParams = [];
    /**
     * @var callable
     */
    private $runnable;

    /**
     * CoreMethod constructor.
     * @param string $name Ex: create,update,delete
     * @param string $method get,post
     * @param string $map url map, use {name} for the name of the method type
     * @param array $otherParams Array of other params required Ex: ['abc' => true] Key is name, value is requiered
     * @param callable $runnable that is runnable, Variable $params wilol be passed to the first pos
     */
    public function __construct($name, $method, $map, array $otherParams, callable $runnable)
    {
        $this->name = $name;
        $this->method = $method;
        $this->map = $map;
        $this->otherParams = $otherParams;
        $this->runnable = $runnable;

        Methods::register($this);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getOtherParams()
    {
        return $this->otherParams;
    }

    /**
     * @param array $otherParams
     */
    public function setOtherParams($otherParams)
    {
        $this->otherParams = $otherParams;
    }

    /**
     * @return mixed
     */
    public function getRunnable()
    {
        return $this->runnable;
    }

    /**
     * @param mixed $runnable
     */
    public function setRunnable($runnable)
    {
        $this->runnable = $runnable;
    }

    /**
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param string $map
     */
    public function setMap($map)
    {
        $this->map = $map;
    }
}

/**
 * Class MethodLink
 * @package Utils
 */
class MethodLink
{

    /**
     * @var string
     */
    private $linkToMethodName;
    /**
     * @var array
     */
    private $params = [];
    /**
     * @var null
     */
    private $default = null;

    /**
     * MethodLink constructor.
     * @param string $linkToMethodName Name of the method to link this to, must be loaded by the model
     * @param array $params array of parameters to pass into the method
     * @param null $default default value of the model if nothing is found or returned
     */
    public function __construct($linkToMethodName, array $params, $default)
    {
        $this->linkToMethodName = $linkToMethodName;
        $this->params = $params;
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getLinkToMethodName()
    {
        return $this->linkToMethodName;
    }

    /**
     * @param mixed $linkToMethodName
     */
    public function setLinkToMethodName($linkToMethodName)
    {
        $this->linkToMethodName = $linkToMethodName;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param null $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

}

?>