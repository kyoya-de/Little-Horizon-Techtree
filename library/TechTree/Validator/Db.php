<?php
abstract class TechTree_Validator_Db
{
    /**
     * Database connection
     * 
     * @var PDO
     */
    protected $_dbObject = null;
    
    protected $_tableName = null;
    
    protected $_columnName = null;
    
    protected $_isValid = null;
    
    function __construct(PDO $dbObject, $tableName, $columnName)
    {
        $this->_dbObject = $dbObject;
        $this->_tableName = $tableName;
        $this->_columnName = $columnName;
    }
}
