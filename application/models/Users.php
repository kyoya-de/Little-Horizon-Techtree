<?php
/**
 * This file is a part of the Little Horizon Community TechTree project.
 * The whole project is licensed under the terms of the MIT license.
 * Take a look at the LICENSE file for information your rights.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage Models
 * @version    4.1.2
 * @author     Stefan Krenz
 */

/**
 * Provides methods to manage users.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage Models
 */
class Application_Model_Users extends TechTree_Db_Model
{
    /**
     * Retrieves a array that contains the administrators.
     * The key is the user ID and the value is the name of the admin.
     *
     * @return array
     */
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

    /**
     * Retrieves the states of bug tickets.
     *
     * @return array
     */
    public function getStates()
    {
        $statesSql = 'SELECT `COLUMN_COMMENT` FROM ' .
                     '`information_schema`.`COLUMNS` WHERE ' .
                     '`TABLE_SCHEMA` = \'' . $this->_dbObject->getDbName() . '\' AND ' .
                     '`TABLE_NAME` = \'tt_bugs\' AND `COLUMN_NAME` = \'status\'';
        $pdoState  = $this->_dbObject->query($statesSql);
        $row       = $pdoState->fetch(PDO::FETCH_ASSOC);
        $result    = array();
        foreach (explode(',', $row['COLUMN_COMMENT']) as $states) {
            list($stateId, $stateName) = explode('=', $states);
            $result[$stateId] = $stateName;
        }
        return $result;
    }

    /**
     * Retrieves an array with currently active users.
     * The key is the user ID and the value is the name of the users.
     *
     * @param string $userId Exclude this user ID from the result list.
     *
     * @return array
     */
    public function getActiveUsers($userId = null)
    {
        $usersSql = 'SELECT `id`, `username` FROM `tt_users` WHERE `active` = 1';
        if ($userId !== null) {
            $usersSql .= ' AND NOT `id` = ' . $userId;
        }
        $usersSql .= ' ORDER BY `username`';
        $pdoState = $this->_dbObject->query($usersSql);
        $users    = array();
        while ($row = $pdoState->fetch(PDO::FETCH_ASSOC)) {
            $users[$row['id']] = $row['username'];
        }

        return $users;
    }
}
