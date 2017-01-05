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
 * Class Models
 * @package Utils
 */
class Models
{

    /**
     * @var
     */
    private static $models;

    /**
     * @param Model $m
     */
    static function register(Model $m)
    {
        self::$models[$m->getName()] = $m;
    }

    /**
     * @return mixed
     */
    static function getAll()
    {
        return self::$models;
    }

    /**
     * @param $name
     * @return bool
     */
    static function existing($name)
    {
        if (isset(self::$models[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return mixed
     */
    static function get($name)
    {
        return self::$models[$name];
    }

}

new Models();

/**
 * Class Model
 * @package Utils
 */
class Model
{

    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $methods;
    /**
     * @var
     */
    private $model;
    /**
     * @var array
     */
    private $rules = [];

    /**
     * Model constructor.
     * @param $name
     * @param $additionalMethods
     * @param $model
     * @param array $rules
     * @internal param $methods
     */
    public function __construct($name, $additionalMethods, $model, $rules = [])
    {
        $this->name = $name;
        $this->methods = $additionalMethods;
        $this->model = $model;
        $this->rules = $rules;

        Models::register($this);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param mixed $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}

?>