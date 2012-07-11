<?php
class Application_Model_Admin_Log extends TechTree_Db_Model
{
    public function getLog(array $filter = array())
    {
        $logSql = 'SELECT log.`id`, user.`username`, user.`id` userid, ' .
                  'unit.`dname`, unit.`name`, log.`action`, log.`timestamp` FROM ' .
                  '`tt_log` log LEFT JOIN `tt_users` user ON ' .
                  'user.`id` = log.`userId` LEFT JOIN `tt_units` unit ON ' .
                  'unit.`name` = log.`name`';
        $logSql .= $this->_getFilterSql($filter);
        $pdoState = $this->_dbObject->query($logSql);
        $log      = array();
        if ($pdoState === false) {
            return $log;
        }

        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $log[$row['id']] = array(
                'user'     => $row['username'],
                'item'     => $row['dname'],
                'action'   => $row['action'],
                'objectid' => $row['name'],
                'userid'   => $row['userid'],
                'time'     => TechTree_Utils::mysqlTs2UnixTs(
                    $row['timestamp'],
                    'd.m.Y H:i:s'
                ),
            );
        }

        return $log;
    }

    public function getLogActions(array $filter = array())
    {
        $sql = 'SELECT DISTINCT `action` FROM `tt_log` log';
        $sql .= $this->_getFilterSql($filter);
        $pdoState   = $this->_dbObject->query($sql);
        $logActions = array();
        if ($pdoState === false) {
            return $logActions;
        }

        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $logActions[] = $row['action'];
        }

        return $logActions;
    }

    public function getLogObjects(array $filter = array())
    {
        $sql = 'SELECT DISTINCT log.`name`, objects.`dname` FROM ' .
               '`tt_log` log LEFT JOIN `tt_units` objects ON ' .
               'objects.`name` = log.`name`';
        $sql .= $this->_getFilterSql($filter);
        $pdoState   = $this->_dbObject->query($sql);
        $logObjects = array();
        if ($pdoState === false) {
            return $logObjects;
        }

        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $logObjects[$row['name']] = $row['dname'];
        }

        return $logObjects;
    }

    public function getLogUsers(array $filter = array())
    {
        $sql = 'SELECT DISTINCT log.`userId`, users.`username` FROM ' .
               '`tt_log` log LEFT JOIN `tt_users` users ON ' .
               'users.`id` = log.`userId`';
        $sql .= $this->_getFilterSql($filter);
        $pdoState = $this->_dbObject->query($sql);
        $logUsers = array();
        if ($pdoState === false) {
            return $logUsers;
        }

        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $logUsers[$row['userId']] = $row['username'];
        }

        return $logUsers;
    }

    public function getLogDates(array $filter = array())
    {
        $sql = 'SELECT DISTINCT DATE(`timestamp`) \'date\' FROM `tt_log` log';
        $sql .= $this->_getFilterSql($filter);
        $pdoState = $this->_dbObject->query($sql);
        $logDates = array();
        if ($pdoState === false) {
            return $logDates;
        }

        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $logDates[] = implode(
                '.',
                array_reverse(explode('-', $row['date']))
            );
        }

        return $logDates;
    }

    public function clearLog()
    {
        $clearSql = 'TRUNCATE TABLE `tt_log`; ' .
                    'ALTER TABLE `tt_log` AUTO_INCREMENT = 1';
        $this->_dbObject->query($clearSql);
    }

    private function _getFilterSql(array $filter, $hasWhere = false)
    {
        $filterSql = '';
        if (!$hasWhere) {
            $filterSql .= ' WHERE 1';
        }
        foreach ($filter as $key => $value) {
            $filterSql .= ' AND ' . $key . ' = ' .
                          $this->_dbObject->quote($value);
        }
        return $filterSql;
    }
}
