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

/**
 * Default Model, for a user. A model is used to design a data base design, and each key can have a default value and settings.
 *
 * Ex: 'id' => [DEFAULT,[SETTINGS_ARRAY]],
 *
 * This would make open the following URL Mapping
 *
 * https://domain.com/default/...
 *          create
 *          delete
 *          find?q=SOMESTRING
 *          {id}/get
 *          list
 *          {id}/update
 *          {id}/touch
 *
 */

new \Utils\Model("default", [
    new \Utils\Method("demoMethodForJustThisModel", "post", "/{id}/touch", [], function (\Utils\Model $model, $params) {
        new APIReturn([
            "Hellow World!"
        ]);
    })
], [
    "id" => [null, [MODEL_PRIMARY_ID, MODEL_REQUIRE]],
    "name" => [null, [MODEL_FINDABLE, MODEL_REQUIRE]],
    "created" => [Utils::makeDBTimeStamp(time()), [MODEL_REQUIRE]]
]);

?>