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
     * @var TechTree_Bugs
     */
    private static $_instance = null;

    /**
     * Disabled constructor, because we have completely static class.
     *
     * @return TechTree_Bugs
     */
    private function __construct()
    {
        $this->_dbObject = TechTree_Db_Model::getDefaultDbConnection();
    }

    /**
     * Disabled cloning, because we have completely static class.
     *
     * @return TechTree_Bugs
     */
    private function __clone()
    {
    }

    /**
     * Returns the class instance.
     *
     * @static
     *
     * @return TechTree_Bugs
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
}
