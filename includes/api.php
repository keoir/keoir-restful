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
 * Class API
 */
class API
{

    /**
     * @var null
     */
    static $parentMethod = null;
    /**
     * @var array
     */
    static $map = [];
    /**
     * @var array
     */
    static $loadedMethods = [];
    /**
     * @var array
     */
    static $params = [];

    /**
     * @return array
     */
    static function makeURLSchema()
    {
        $map = [];
        $models = \Utils\Models::getAll();
        foreach ($models as $model) {
            if ($model instanceof \Utils\Model) {
                $methods = [];

                /*
                 * Import Default Methods
                 * */
                foreach (\Utils\Methods::getAll() as $method) {
                    if ($method instanceof \Utils\Method) {
                        $methods[$method->getName()] = $method;
                    }
                }

                /*
                 * Import Per Model Methods
                 * */
                foreach ($model->getMethods() as $method) {
                    if ($method instanceof \Utils\Method) {
                        $methods[$method->getName()] = $method;
                    }
                }


                /*
                 * Generate URL Mapping for Methods
                 * */

                foreach ($methods as $method) {
                    if ($method instanceof \Utils\Method) {
                        $urlMap = strtoupper($method->getMethod()) . " - /" . $model->getName() . $method->getMap();
                        self::$loadedMethods[$model->getName() . '::' . $method->getName()] = $method;
                        $map[$urlMap] = $model->getName() . '::' . $method->getName();
                    }
                }
            }
        }

        self::$map = $map;
        return self::$map;
    }

    /**
     * @param \Utils\Model $m
     * @param \RedBeanPHP\OODBBean $dbObj
     * @return array
     */
    static function buildReturnFromModel(\Utils\Model $m, \RedBeanPHP\OODBBean $dbObj)
    {
        $r = [];

        foreach ($m->getModel() as $key => $data) {
            if ($data instanceof \Utils\MethodLink) {
                $r[$key] = self::simulateCall(\Utils\Methods::get($data->getLinkToMethodName()), $m, self::$params);
            } else {
                $r[$key] = $dbObj->{$key};
            }
        }

        return $r;
    }

    /**
     * @param \Utils\Method $method
     * @param \Utils\Model $model
     * @param array $params
     * @return array
     */
    static function simulateCall(\Utils\Method $method, \Utils\Model $model, $params = [])
    {
        ob_start();
        call_user_func($method->getRunnable(), $model, (object)$params, []);
        $r = ob_get_contents();
        ob_clean();

        $r = json_decode($r, true);
        if ($r['success']) {
            return $r['data'];
        }
        return [];
    }

    /**
     * @param null $stripFromURI
     */
    static function findMethodFromURI($stripFromURI = null)
    {
        // Current URL
        $currentURI = array_reverse(Utils::cleanArray(explode("/", ($stripFromURI == null) ? explode("?", $_SERVER['REQUEST_URI'])[0] : str_replace($stripFromURI, "", explode("?", $_SERVER['REQUEST_URI'])[0]))));

        $matches = [];
        $uriTemplates = [];
        foreach (self::$map as $loc => $methodIdentifier) {
            preg_match('/([A-Z]*)\s*\-\s*(.*)/', $loc, $parts);

            $requestType = $parts[1];
            $url = $parts[2];

            $methodMap = array_reverse(Utils::cleanArray(explode("/", $url)));

            $score = 0;
            if (count($currentURI) == count($methodMap) && strtoupper($_SERVER['REQUEST_METHOD']) == $requestType) {
                foreach ($methodMap as $i => $part) {
                    if (preg_match('/\{.*?\}/', $part)) {
                        $score++;
                    } else if ($part == $currentURI[$i]) {
                        $score += 2;
                    } else {
                        $score = 0;
                        break;
                    }
                }
            }

            if ($score > 0) {
                $matches[$methodIdentifier] = $score;
                $uriTemplates[$methodIdentifier] = $url;
            }
        }

        arsort($matches);

        if (count($matches) > 0) {
            $firstPick = array_keys($matches)[0];
            self::$params = self::pullParamsFromURI($uriTemplates[$firstPick]);
            self::runMethod($firstPick);
        } else {
            new ErrorType\NoPath();
        }
    }

    /**
     * @param $uriTemplate
     * @param null $stripFromURI
     * @return array
     */
    static function pullParamsFromURI($uriTemplate, $stripFromURI = null)
    {
        $currentURI = array_reverse(Utils::cleanArray(explode("/", ($stripFromURI == null) ? explode("?", $_SERVER['REQUEST_URI'])[0] : str_replace($stripFromURI, "", explode("?", $_SERVER['REQUEST_URI'])[0]))));
        $uriTemplate = array_reverse(Utils::cleanArray(explode("/", $uriTemplate)));

        $p = [];

        foreach ($uriTemplate as $pos => $part) {
            if (preg_match('/\{(.*?)\}/', $part, $m)) {
                $p[$m[1]] = $currentURI[$pos];
            }
        }

        return $p;
    }

    /**
     * @param $methodIdentifier
     */
    static function runMethod($methodIdentifier)
    {
        $parts = explode("::", $methodIdentifier);
        $model = $parts[0];
        $method = $parts[1];

        if (\Utils\Models::existing($model) && \Utils\Methods::existing($method) && isset(self::$loadedMethods[$methodIdentifier])) {
            $m = self::$loadedMethods[$methodIdentifier];
            if ($m instanceof \Utils\Method) {
                $p = self::buildParams($m);

                self::$parentMethod = $m;

                call_user_func($m->getRunnable(), \Utils\Models::get($model), $p, self::buildObjectEditMap(\Utils\Models::get($model), $p));
            } else {
                new \ErrorType\NotFound();
            }
        } else {
            new ErrorType\NotFound();
        }
    }

    /**
     * @param \Utils\Method $method
     * @return object
     */
    static function buildParams(\Utils\Method $method)
    {
        $params = array_merge($_POST, $_GET, self::$params);

        /*
         * Get Model Settings
         * */
        foreach ($method->getOtherParams() as $key => $value) {
            $params[$key] = self::buildParam($key, $value, $params);
        }

        return self::$params = (object)$params;
    }

    /**
     * @param $key
     * @param $value
     * @param array $paramArray
     * @return int|mixed
     */
    static function buildParam($key, $value, $paramArray = [])
    {
        $require = false;
        $default = MODEL_NO_DEFAULT;

        if (is_array($value)) {
            if (count($value) > 0) {
                foreach ($value as $part) {
                    if (is_array($part)) {
                        /*
                         * Is Settings
                         * */
                        foreach ($part as $pice) {
                            switch ($pice) {
                                case MODEL_REQUIRE:
                                    $require = true;
                                    break;
                            }
                        }
                    } else if (!is_array($part)) {
                        /*
                         * Is Default Value
                         * */
                        $default = $part;
                    }
                }
            }
        } else if ($value != MODEL_NO_DEFAULT) {
            $default = $value;
        }

        if ($require && $default == MODEL_NO_DEFAULT) {
            new InvalidParameter($key);
        } else if ($default != MODEL_NO_DEFAULT && !isset($paramArray[$key])) {
            return $default;
        } else {
            return $paramArray[$key];
        }
    }

    /**
     * @param \Utils\Model $m
     * @param $params
     * @param bool $includeLinks
     * @return array
     */
    static function buildObjectEditMap(\Utils\Model $m, $params, $includeLinks = false)
    {
        $r = [];
        $primary = "id";
        $model = $m->getModel();

        foreach ($model as $key => $settings) {
            $finadable = false;
            $locked = false;
            $isPrimary = false;
            $default = null;
            $require = false;
            $unique = false;
            if (is_array($settings)) {
                if (count($settings) > 0) {
                    foreach ($settings as $part) {
                        if (is_array($part)) {
                            /*
                             * Is Settings
                             * */
                            foreach ($part as $pice) {
                                switch ($pice) {
                                    case MODEL_FINDABLE:
                                        $finadable = true;
                                        break;
                                    case MODEL_LOCK:
                                        $locked = true;
                                        break;
                                    case MODEL_PRIMARY_ID:
                                        $isPrimary = true;
                                        $primary = $key;
                                        break;
                                    case MODEL_REQUIRE:
                                        $require = true;
                                        break;
                                    case MODEL_UNIQUE:
                                        $unique = true;
                                        break;
                                }
                            }
                        } else if (!is_array($part)) {
                            /*
                             * Is Default Value
                             * */
                            $default = $part;
                        }
                    }
                }

                $r[$key] = [
                    "default" => $default,
                    "value" => (isset($params->{$key})) ? $params->{$key} : null,
                    "primary" => $isPrimary,
                    "locked" => $locked,
                    "findable" => $finadable,
                    "require" => $require,
                    "unique" => $unique
                ];
            } else if ($settings instanceof \Utils\MethodLink && $includeLinks) {
                $r[$key] = "api::" . self::buildURL(\Utils\Methods::get($settings->getLinkToMethodName()), $params);
            }
        }

        return $r;
    }

    /**
     * @param \Utils\Method $method
     * @param \Utils\Model $model
     * @param array $params
     * @return mixed|string
     */
    static function buildURL(\Utils\Method $method, \Utils\Model $model, $params = [])
    {
        $s = "https://" . $_SERVER['HTTP_HOST'] . "/" . $model->getName() . $method->getMap();

        preg_match_all('/\{(.*?)\}/', $s, $m, PREG_SET_ORDER);
        foreach ($m as $i) {
            $full = $i[0];
            $id = $i[1];

            if (isset($params->{$id})) {
                $s = str_replace($full, $params->{$id}, $s);
            }
        }

        return $s;
    }

    /**
     * @param \RedBeanPHP\OODBBean $rbObj
     * @param \Utils\Model $m
     * @param $params
     * @param bool $overrideLocked
     * @return \RedBeanPHP\OODBBean
     */
    static function alter(\RedBeanPHP\OODBBean $rbObj, \Utils\Model $m, $params, $overrideLocked = false)
    {
        $rules = self::buildObjectEditMap($m, $params);
        foreach ($rules as $key => $rule) {
            if (substr($key, 0, 1) != "_" && $key != "id") {
                if (!$rule['locked'] || $overrideLocked) {
                    if (isset($params->{$key})) {
                        $rbObj->{$key} = $params->{$key};
                    } else if (isset($rule['default'])) {
                        $rbObj->{$key} = $rule['default'];
                    } else {
                        new InvalidParameter($key);
                    }
                }
                if ($rule['unique']) {
                    if (R::count($m->getName(), "$key = ?", [
                            $rbObj->{$key}
                        ]) > 0
                    ) {
                        new DuplicateError($key);
                    }
                }
            }
        }

        return $rbObj;
    }

    /**
     *
     */
    static function autoLoadModels()
    {
        new \Utils\Load("/models", '/.*\.php/');
    }

    /**
     *
     */
    static function autoLoadMethods()
    {
        new \Utils\Load("/methods", '/.*\.php/');
    }

}

new API();

?>