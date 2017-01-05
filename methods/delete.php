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
 * URL Example : https://domain.com/modelName/{id}/delete
 */


new \Utils\Method("delete", "delete", "/{id}/delete", [], function (\Utils\Model $model, $params) {
    if (R::count($model->getName(), "id = ?", [
            $params->id
        ]) > 0
    ) {
        R::trash($model->getName(), $params->id);

        new APIReturn([]);
    } else {
        new \ErrorType\NotFound();
    }
});

?>