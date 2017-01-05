<?

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
class DB
{

    /**
     * @var null
     */
    static $connection;
    /**
     * @var
     */
    static $connectionString;
    /**
     * @var array
     */
    static $openConnections = [];

    /**
     * @var
     */
    private static $host;
    /**
     * @var
     */
    private static $user;
    /**
     * @var
     */
    private static $pass;
    /**
     * @var
     */
    private static $dbName;

    /**
     * DB constructor.
     * @param $host
     * @param $user
     * @param $pass
     * @param $db
     */
    public function __construct($host, $user, $pass, $db)
    {

        //Set Local Vars
        self::$host = $host;
        self::$user = $user;
        self::$pass = $pass;
        self::$dbName = $db;

        self::$connection = null;

        //Enable of File
        self::open();
    }

    /**
     * Open SQL Connection
     */
    private static function open()
    {
        if (self::$connection == null) {
            try {
                /*
                 * Standard MYSQL
                 * */
//                self::$connectionString = 'mysql:host=' . self::$host . ';dbname=' . self::$dbName;

                /*
                 * Google App Engine SQL
                 * */
                self::$connectionString = 'mysql:unix_socket=/cloudsql/' . Config::$dbHost . ';dbname=' . Config::$dbName;

                self::$connection = new PDO(self::$connectionString, self::$user, self::$pass);

            } catch (PDOException $e) {
                echo "Database Error; db != true && count(errors) > 0; $e;";
                exit;
            }
        }
    }

    /**
     * Close SQL Connection
     */
    static function close()
    {
        if (count(self::$openConnections) > 0) {
            foreach (self::$openConnections as $c) {
                $c->closeCursor();
            }
        }

        if (self::$connection != null) {
            self::$connection = null;
        }
    }
}

class Query extends DB
{

    /**
     * @var
     */
    private $query;
    /**
     * @var
     */
    private $qStatment;

    /**
     * Query constructor.
     * @param $q
     * @param array $data
     * @param bool $debug prints out the SQL statment on website
     * @internal param string $timeZone
     */
    function __construct($q, $data = [], $debug = false)
    {
        if (self::$connection == null) {
            new DB(Config::$dbHost, Config::$dbUsername, Config::$dbPassword, Config::$dbName);
        }

        $pdo = self::$connection;

        $this->qStatment = $q;

        if ($debug) {
            echo $this->buildPreviewFromPrePared($q, $data);
        }

        if (count($data) > 0) {
            $r = $pdo->prepare($q);
            $o = $r->execute($data);
        } else {
            $r = $pdo->prepare($q);
            $o = $r->execute();
        }

        if ($o == false) {
            new Exception("SQL Error (" . $this->buildPreviewFromPrePared($q, $data) . ")", 0);
            exit;
        }

        $this->query = $r;

        parent::$openConnections[] = $r;

        return $this;
    }

    /**
     * @param $statment
     * @param $data
     * @return mixed
     */
    private function buildPreviewFromPrePared($statment, $data)
    {
        foreach ($data as $d) {
            $statment = preg_replace("/\?/", "'" . $d . "'", $statment, 1);
        }

        return $statment;
    }

    /**
     * @return bool
     */
    function success()
    {
        $o = $this->getObj();
        if ($o) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    function getObj()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    function getArray()
    {
        $a = [];
        while ($r = $this->query->fetch()) {
            foreach ($r as $k => $v) {
                $a[$k] = stripslashes($v);
            }
        }

        return $a;
    }

    /**
     * @return array
     */
    function getMultiArray()
    {
        $a = [];
        while ($r = $this->query->fetch()) {
            $b = [];
            foreach ($r as $k => $v) {
                $b[$k] = stripslashes($v);
            }
            $a[] = $b;
        }
        return $a;
    }

    /**
     * @return mixed
     */
    function getRowCount()
    {
        return $this->query->rowCount();
    }

    /**
     * @return mixed
     */
    function getID()
    {
        return parent::$connection->lastInsertId();
    }

    /**
     * @return mixed
     */
    function getStatment()
    {
        return $this->qStatment;
    }

    /**
     *
     */
    function closeCursor()
    {
        $this->query->closeCursor();
    }
}

?>