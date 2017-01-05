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

/*
 * Model Rules
 * */

const MODEL_NO_DEFAULT = -999999;
const MODEL_PRIMARY_ID = 1;
const MODEL_LOCK = 2;
const MODEL_REQUIRE = 3;
const MODEL_FINDABLE = 4;
const MODEL_UNIQUE = 5;

/*
 * Load Things :)
 * */

require_once BASE . "/config.php";

require_once BASE . "/connector/connect.php";
require_once BASE . "/connector/qb.php";
require_once BASE . "/connector/rb.php";

new DB(Config::$dbHost, Config::$dbUser, Config::$dbPass, Config::$dbName);
R::setup(DB::$connectionString, Config::$dbUser, Config::$dbPass);

require_once BASE . "/includes/utils.php";
require_once BASE . "/includes/loader.php";
require_once BASE . "/includes/methods.php";
require_once BASE . "/includes/models.php";
require_once BASE . "/includes/return.php";

new \Utils\Load("/errors", '/.*\.php/');

require_once BASE . "/includes/api.php";

API::autoLoadMethods();
API::autoLoadModels();

?>