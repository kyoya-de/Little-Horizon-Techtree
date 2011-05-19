<?php
class TechTree_Bugs
{
    /**
     * Database connection
     * 
     * @var PDO
     */
    private $_dbObject = null;
    
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
    
    public function getBugs()
    {
        $bugsSql = 'SELECT bug.`id`, bug.`title`, bug.`description`, ' .
            'bug.`status`, bug.`assignId`, reporter.`username` ' .
            'FROM `tt_bugs` bug LEFT JOIN `tt_users` reporter ON ' .
            'reporter.`id` = bug.`reporterId` ORDER BY `priority` DESC, ' .
            '`id` DESC';
        $pdoState = $this->_dbObject->query($bugsSql);
        if ($pdoState === false) {
            return array();
        }
        
        $bugs = array();
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $bugs[$row['id']] = array(
                'title' => $row['title'],
                'description' => $row['description'],
                'status' => $row['status'],
                'reporter' => $row['username'],
                'assigned' => $row['assignId'],
            );
        }
        return $bugs;
    }
    
    public function reportBug($userId, $summary, $details)
    {
        $reportSql = 'INSERT INTO `tt_bugs` (`reporterId`, `title`, ' .
            '`description`) VALUES (' . $this->_dbObject->quote($userId) .
            ', ' . $this->_dbObject->quote($summary) . ', ' .
            $this->_dbObject->quote($details) . ')';
        $this->_dbObject->query($reportSql);
    }
}
