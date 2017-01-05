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
 * Default Create method
 * URL Example : https://domain.com/modelName/create
 */

new \Utils\Method("create", "post", "/create", [], function (\Utils\Model $model, $parms, $modelRules) {
    $o = R::dispense($model->getName());

    $o = API::alter($o, $model, $parms, true);

    $id = R::store($o);

    if ($id != 0) {
        $new = R::load($model->getName(), $id);

        $r = API::buildReturnFromModel($model, $new);

        new APIReturn($r);
    } else {
        new InternalError();
    }
});
?>