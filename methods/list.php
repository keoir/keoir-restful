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
 * Default Delete method
 * URL Example : https://domain.com/modelName/list
 */

new \Utils\Method("list", "get", "/list", [
    "page" => [1, [MODEL_REQUIRE]],
    "per_page" => [10, [MODEL_REQUIRE]],
    "sort_by" => ["id", [MODEL_REQUIRE]],
    "sort_order" => ["DESC", [MODEL_REQUIRE]]
], function (\Utils\Model $model, $params) {
    $r = [];

    $startingPointer = ($params->page * $params->per_page) - $params->per_page;
    $endingPointer = ($params->page * $params->per_page);

    $totalFound = R::count($model->getName());

    $many = R::findAll($model->getName(), "ORDER BY $params->sort_by $params->sort_order LIMIT $startingPointer,$params->per_page");

    foreach ($many as $one) {
        $r[] = API::simulateCall(\Utils\Methods::get("get"), $model, [
            "id" => $one['id']
        ]);
    }

    $final = [
        "current_page" => $params->page,
        "per_page" => $params->per_page,
        "total_pages" => ceil($totalFound / $params->per_page),
        "total_objects" => $totalFound,
        "return" => $r
    ];

    new APIReturn($final);
});

?>