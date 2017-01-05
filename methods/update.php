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
 * URL Example : https://domain.com/modelName/{id}?username={username}
 */

new \Utils\Method("update", "post", "/{id}", [], function (\Utils\Model $model, $params) {
    if (R::count($model->getName(), "id = ?", [
            $params->id
        ]) > 0
    ) {
        $o = R::load($model->getName(), $params->id);
        $o = API::alter($o, $model, $params);

        R::store($o);

        $r = API::buildReturnFromModel($model, $o);

        new APIReturn($r);
    } else {
        new \ErrorType\NotFound();
    }
});

?>