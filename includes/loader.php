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
 * Class Load
 * @package Utils
 */
class Load
{

    /**
     * @var
     */
    private $dir;
    /**
     * @var
     */
    private $regex;

    /**
     * Load constructor.
     * @param $dir
     * @param $regex
     */
    public function __construct($dir, $regex)
    {
        $this->dir = $dir;
        $this->regex = $regex;

        $this->load($this->dir);
    }

    /**
     * @param $dir
     */
    private function load($dir)
    {
        foreach (glob(BASE . $dir . "/*") as $file) {
            if (is_dir($file)) {
                $this->load($file);
            } else if (is_file($file)) {
                if (preg_match($this->regex, $file)) {
                    require_once $file;
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return mixed
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * @param mixed $regex
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
    }
}

?>