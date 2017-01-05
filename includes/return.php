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
 * Class APIReturn
 */
class APIReturn
{

    /**
     * @var bool
     */
    private $success = false;
    /**
     * @var array
     */
    private $data = [];

    /**
     * APIReturn constructor.
     * @param array $data
     * @internal param bool $success
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->success = true;
            $this->data = $data;
        } else if ($data instanceof APIError) {
            $this->success = false;
            $this->data = [
                "code" => $data->getCode(),
                "name" => $data->getName(),
                "message" => $data->getMessage()
            ];
        }

        $return = [
            "success" => $this->success,
            "data" => $this->data
        ];

        echo json_encode($return);
        if (!ob_get_status()) {
            exit;
        }
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}

/**
 * Class APIError
 */
class APIError
{

    /**
     * @var
     */
    private $code;
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $message;

    /**
     * APIError constructor.
     * @param $code
     * @param $name
     * @param $message
     */
    public function __construct($code, $name, $message)
    {
        $this->code = $code;
        $this->name = $name;
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}

?>