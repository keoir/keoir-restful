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

abstract class ErrorTemplate
{
    public $code;
    public $name;
    public $message;

    /**
     * Error constructor.
     * @param $code
     * @param $name
     * @param $message
     */
    public function __construct($code, $name, $message)
    {
        $this->code = $code;
        $this->name = $name;
        $this->message = $message;

        new APIReturn(new APIError($this->code, $this->name, $this->message));
    }
}

?>