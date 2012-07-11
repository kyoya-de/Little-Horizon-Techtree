<?php
class TechTree_Log
{
    /**
     * Database connection
     *
     * @var PDO
     */
    private static $_dbObject = null;

    /**
     * Disabled constructor, because we have completely static class.
     *
     * @return TechTree_Log
     */
    private function __construct()
    {
    }

    /**
     * Disabled cloning, because we have completely static class.
     *
     * @return TechTree_Log
     */
    private function __clone()
    {
    }

    /**
     * Writes an action into the log table.
     *
     * @param int    $userId ID of the user.
     * @param string $object Effected object.
     * @param string $action Executed action.
     *
     * @static
     *
     * @return void
     */
    public static function log($userId, $object, $action)
    {
        if (self::$_dbObject === null) {
            self::$_dbObject = TechTree_Db_Model::getDefaultDbConnection();
        }
        $logSql    = 'INSERT INTO `tt_log` (`userId`, `name`, `action`) VALUES ' .
                     '(:userId, :object, :action)';
        $pdoState  = self::$_dbObject->prepare($logSql);
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
