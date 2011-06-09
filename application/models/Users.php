<?php
class Application_Model_Users extends TechTree_Db_Model
{
    public function getAdmins()
    {
        $adminSql = 'SELECT `id`, `username` FROM `tt_users` WHERE ' .
            '`account_type` = \'admin\'';
        $pdoState = $this->_dbObject->query($adminSql);
        if ($pdoState === false) {
            return array();
        }
        
        $admins = array(
            0 => 'Nicht Zugewiesen',
        );
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $admins[$row['id']] = $row['username'];
        }
        
        return $admins;
    }
    public function getStates()
    {
        $statesSql = 'SELECT `COLUMN_COMMENT` FROM ' .
            '`information_schema`.`COLUMNS` WHERE '.
            '`TABLE_SCHEMA` = \'' . $this->_dbObject->getDbName() . '\' AND ' .
            '`TABLE_NAME` = \'tt_bugs\' AND `COLUMN_NAME` = \'status\'';
        $pdoState = $this->_dbObject->query($statesSql);
        $row = $pdoState->fetch(PDO::FETCH_ASSOC);
        $result = array();
        foreach (explode(',', $row['COLUMN_COMMENT']) as $states) {
            list($stateId, $stateName) = explode('=', $states);
            $result[$stateId] = $stateName;
        }
        return $result;
    }
    public function getActiveUsers($userId = null)
    {
        $usersSql = 'SELECT `id`, `username` FROM `tt_users` WHERE `active` = 1';
        if ($userId !== null) {
            $usersSql .= ' AND NOT `id` = ' . $userId;
        }
        $usersSql .= ' ORDER BY `username`';
        $pdoState = $this->_dbObject->query($usersSql);
        $users = array();
        while ($row = $pdoState->fetch(PDO::FETCH_ASSOC)) {
            $users[$row['id']] = $row['username'];
        }

        return $users;
    }
}
