<?php
abstract class TechTree_Db_Model
{
    /**
     * Database connection
     *
     * @var PDO
     */
    protected $_dbObject = null;

    private static $_defaultDbObject = null;

    /**
     * Class constructor. Sets the database connection and invokes the "init" method.
     * WARNING: Do not overwrite without calling the parent.
     *
     * @return TechTree_Db_Model
     */
    public function __construct()
    {
        $this->_dbObject = self::$_defaultDbObject;
        $this->init();
    }

    /**
     * Initialize method for the models. Will be overwritten if needed.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Sets the default database connection.
     *
     * @param PDO $dbObject Instance of a PDO object which represents the database connection.
     *
     * @static
     *
     * @return void
     */
    public static function setDefaultDbConnection(PDO $dbObject)
    {
        self::$_defaultDbObject = $dbObject;
    }

    /**
     * Returns the default database connection.
     *
     * @static
     *
     * @return PDO
     */
    public static function getDefaultDbConnection()
    {
        return self::$_defaultDbObject;
    }

    /**
     * Fills a query with data. Workaround if RDBMS does not support "prepared statements".
     *
     * @param string $query MySQL-Query.
     * @param array  $data  Data to insert.
     *
     * @return mixed
     */
    public function buildQuery($query, array $data)
    {
        foreach ($data as $key => $value) {
            $val   = $this->_dbObject->quote($value);
            $query = str_replace(':' . $key, $val, $query);
        }
        return $query;
    }
}
