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
 * Default Get method
 * URL Example : https://domain.com/modelName/{id}
 */

new \Utils\Method("get", "get", "/{id}", [], function (\Utils\Model $model, $params, $modelRules) {
    /*
     * Check if object exist
     * */
    if (R::count($model->getName(), "WHERE id = ?", [
            $params->id
        ]) > 0
    ) {
        $o = R::load($model->getName(), $params->id);

        $r = API::buildReturnFromModel($model, $o);

        new APIReturn($r);
    } else {
        new \ErrorType\NotFound();
    }
});

?>