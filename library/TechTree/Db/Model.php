<?php
abstract class TechTree_Db_Model
{
    /**
     * Database connection
     * 
     * @var TechTree_Db
     */
    protected $_dbObject = null;
    
    private static $_defaultDbObject = null;
    
    public function __construct()
    {
        $this->_dbObject = self::$_defaultDbObject;
        $this->init();
    }
    
    public function init()
    {
    }
    
    public static function setDefaultDbConnection(PDO $dbObject)
    {
        self::$_defaultDbObject = $dbObject;
    }
    
    /**
     * @return PDO
     */
    public static function getDefaultDbConnection()
    {
        return self::$_defaultDbObject;
    }
    
    public function buildQuery($query, array $data)
    {
        foreach ($data as $key => $value) {
            $val = $this->_dbObject->quote($value);
            $query = str_replace(':' . $key, $val, $query);
        }
        return $query;
    }
}
