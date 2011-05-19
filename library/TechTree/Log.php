<?php
class TechTree_Log
{
    /**
     * Database connection
     * 
     * @var PDO
     */
    private static $_dbObject = null;
    
    private function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    public static function log($userId, $object, $action)
    {
        if (self::$_dbObject === null) {
            self::$_dbObject = TechTree_Db_Model::getDefaultDbConnection();
        }
        $logSql = 'INSERT INTO `tt_log` (`userId`, `name`, `action`) VALUES ' .
            '(:userId, :object, :action)';
        $pdoState = self::$_dbObject->prepare($logSql);
        $pdoResult = $pdoState->execute(
            array(
                'userId' => $userId,
                'object' => $object,
                'action' => $action,
            )
        );
        if ($pdoResult === false) {
        }
    }
}
