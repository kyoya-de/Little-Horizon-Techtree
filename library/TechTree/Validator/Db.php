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

    /**
     * Initializes a new validator class for database based validation.
     *
     * @param PDO    $dbObject   Instance of a connected PDO class.
     * @param string $tableName  Name of table for the validation.
     * @param string $columnName Name of column for the validation.
     */
    function __construct(PDO $dbObject, $tableName, $columnName)
    {
        $this->_dbObject   = $dbObject;
        $this->_tableName  = $tableName;
        $this->_columnName = $columnName;
    }
}
