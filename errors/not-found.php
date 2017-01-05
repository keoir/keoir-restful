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

namespace ErrorType;

class NotFound extends \ErrorTemplate
{
    public function __construct()
    {
        parent::__construct(404, "No Resource Found", "We are unable to find this resource");
    }
}

?>