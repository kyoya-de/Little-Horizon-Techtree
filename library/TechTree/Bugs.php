<?php
class TechTree_Bugs
{
    /**
     * Database connection
     * 
     * @var PDO
     */
    private $_dbObject = null;

    /**
     * @var null
     */
    private static $_instance = null;
    
    private function __construct()
    {
        $this->_dbObject = TechTree_Db_Model::getDefaultDbConnection();
    }
    
    private function __clone()
    {
    }


    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
}
