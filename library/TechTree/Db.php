<?php
class TechTree_Db extends PDO
{
    private $_dbName = null;
    
    public function __construct($options)
    {
        if (!isset($options['host'])) {
            throw new TechTree_Db_Exception('You must specify a MySQL host!');
        }
        if (!isset($options['user'])) {
            $options['user'];
        }
        if (!isset($options['pass'])) {
            $options['pass'] = '';
        }
        if (!isset($options['dbName'])) {
            throw new TechTree_Db_Exception(
                'You must specify a MySQL database name!'
            );
        }
        $this->_dbName = $options['dbName'];
        if (!isset($options['options'])) {
            $options['options'] = array();
        }
        $options['options'] += array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        );
        
        parent::__construct(
            sprintf(
                'mysql:dbname=%s;host=%s',
                $options['dbName'],
                $options['host']
            ),
            $options['user'],
            $options['pass'],
            $options['options']
        );
    }
    
    public function getDbName()
    {
        return $this->_dbName;
    }
}
