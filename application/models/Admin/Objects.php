<?php
class Application_Model_Admin_Objects extends TechTree_Db_Model
{
    private function _getItems($name)
    {
        $parentSql = 'SELECT `lft`, `rgt` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($name);
        $pdoState = $this->_dbObject->query($parentSql);
        
        $tree = array();
        if ($pdoState === false) {
            return $tree;
        }
        
        $parentNode = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        $childNodesSql = 'SELECT `id`, `name`, `dname`, `last_update`, ' .
            '`lft`, `rgt` FROM `tt_units` WHERE ' .
            '`lft` > :lft AND `rgt` < :rgt AND `type` = \'item\'';
        $pdoState = $this->_dbObject->prepare($childNodesSql);
        
        $pdoResult = $pdoState->execute($parentNode);
        
        if ($pdoResult == false) {
            return $tree;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $tree[$row['name']] = array(
                'dname' => $row['dname'],
                'id' => $row['id'],
                'update' => TechTree_Utils::mysqlTs2UnixTs(
                    $row['last_update'],
                    'd.m.Y H:i:s'
                ),
                'rgt' => $row['rgt'],
                'lft' => $row['lft'],
            );
        }
        return $tree;
    }
    
    private function _getCategories($name)
    {
        $parentSql = 'SELECT `lft`, `rgt` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($name);
        $pdoState = $this->_dbObject->query($parentSql);
        
        $tree = array();
        if ($pdoState === false) {
            return $tree;
        }
        
        $parentNode = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        $childNodesSql = 'SELECT `id`, `name`, `dname`, `lft`, `rgt` ' .
            'FROM `tt_units` WHERE `lft` > :lft AND `rgt` < :rgt AND ' .
            '`type` = \'category\'';
        $pdoState = $this->_dbObject->prepare($childNodesSql);
        
        $pdoResult = $pdoState->execute($parentNode);
        
        if ($pdoResult == false) {
            return $tree;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $tree[$row['name']] = array(
                'dname' => $row['dname'],
                'id' => $row['id'],
                'rgt' => $row['rgt'],
                'lft' => $row['lft'],
                'childs' => $this->_getItems($row['name'])
            );
        }
        return $tree;
    }
    
    private function _getTypes($name)
    {
        $parentSql = 'SELECT `lft`, `rgt` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($name);
        $pdoState = $this->_dbObject->query($parentSql);
        
        $tree = array();
        if ($pdoState === false) {
            return $tree;
        }
        
        $parentNode = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        $childNodesSql = 'SELECT `id`, `name`, `dname`, `lft`, `rgt` ' .
            'FROM `tt_units` WHERE `lft` > :lft AND `rgt` < :rgt AND ' .
            '`type` = \'type\'';
        $pdoState = $this->_dbObject->prepare($childNodesSql);
        
        $pdoResult = $pdoState->execute($parentNode);
        
        if ($pdoResult == false) {
            return $tree;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $tree[$row['name']] = array(
                'dname' => $row['dname'],
                'id' => $row['id'],
                'rgt' => $row['rgt'],
                'lft' => $row['lft'],
                'childs' => $this->_getCategories($row['name'])
            );
        }
        return $tree;
    }
    
    public function getCompleteTree()
    {
        $rootSql = 'SELECT `id`, `name`, `dname`, `lft`, `rgt` ' .
        'FROM `tt_units` WHERE `type` = \'root\'';
        $pdoState = $this->_dbObject->query($rootSql);
        
        $tree = array();
        if ($pdoState === false) {
            return $tree;
        }
        
        $row = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        $tree[$row['name']] = array(
            'dname' => $row['dname'],
            'id' => $row['id'],
            'rgt' => $row['rgt'],
            'lft' => $row['lft'],
            'childs' => $this->_getTypes($row['name']),
        );
        return $tree;
    }
    
    public function getObject($objectId)
    {
        $objectSql = 'SELECT `id`, `name`, `dname`, `type`, `race`, ' .
            '`comment`, `max_level` maxLevel, `last_update` lastUpdate, ' .
            '`lft`, `rgt` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($objectId);
        $pdoState = $this->_dbObject->query($objectSql);
        
        $object = array();
        if ($pdoState === false) {
            return $object;
        }
        
        $object = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        return $object;
    }
    
    public function objectEdit(array $params)
    {
        unset(
            $params['action'],
            $params['controller'],
            $params['module'],
            $params['doAction']
        );
        
        $objectSql = 'UPDATE `tt_units` SET `name` = :name, `dname` = :dname, '.
            '`type` = :type, `race` = :race, `comment` = :comment, ' .
            '`max_level` = :maxLevel, `last_update` = :lastUpdate, ' .
            '`lft` = :left, `rgt` = :right WHERE `id` = :objectId';
        $pdoState = $this->_dbObject->prepare($objectSql);
        $pdoResult = $pdoState->execute($params);
        return $pdoResult;
    }
    public function objectDelete(array $params)
    {
        
        $deleteSql = 'UPDATE `tt_units` SET `rgt` = `rgt` - 2 ' .
            'WHERE `rgt` > ' . $params['right'] . ';' .
            'UPDATE `tt_units` SET `lft` = `lft` - 2 WHERE `lft` > ' .
            $params['right'] . ';' .
            'DELETE FROM `tt_units` WHERE `id` = ' . $params['objectId'] . ';';
        $pdoState = $this->_dbObject->query($deleteSql);
        if ($pdoState === false) {
            return 0;
        } else {
            return 1;
        }
    }
    
    public function getCategory($categoryId)
    {
        $categorySql = 'SELECT `id`, `name`, `dname`, `type`, `lft`, `rgt` ' .
            'FROM `tt_units` WHERE `id` = ' .
            $this->_dbObject->quote($categoryId);
        $pdoState = $this->_dbObject->query($categorySql);
        
        $category = array();
        if ($pdoState === false) {
            return $category;
        }
        
        $category = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        return $category;
    }
    
    public function categoryEdit(array $params)
    {
        unset(
            $params['action'],
            $params['controller'],
            $params['module'],
            $params['doAction']
        );
        
        $objectSql = 'UPDATE `tt_units` SET `name` = :name, `dname` = :dname, '.
            '`type` = :type, `lft` = :left, `rgt` = :right WHERE ' .
            '`id` = :categoryId';
        $pdoState = $this->_dbObject->prepare($objectSql);
        $pdoResult = $pdoState->execute($params);
        return $pdoResult;
    }
    
    public function categoryDelete(array $params)
    {
        $nextElementSql = 'SELECT `lft`,`name` FROM `tt_units` WHERE `lft` = ' .
            ($params['right'] + 1);
        $pdoState = $this->_dbObject->query($nextElementSql);
        if ($pdoState->rowCount() == 0) {
            $pdoState->closeCursor();
            $nextElementSql = 'SELECT `rgt`,`name` FROM `tt_units` ' .
                'WHERE `lft` < ' . $params['left'] . ' AND ' .
                '`rgt` > ' . $params['right'] . ' ORDER BY `lft` DESC';
            $pdoState = $this->_dbObject->query($nextElementSql);
            $object = $pdoState->fetch(PDO::FETCH_ASSOC);
            $diff = $object['rgt'] - $params['left'];
        } else {
            $pdoState->closeCursor();
            $diff = $object['lft'] - $params['left'];
        }
        
        $deleteSql = 'DELETE FROM `tt_units` ' .
            'WHERE `lft` >= ' . $params['left'] . ' ' .
            'AND `rgt` <= ' . $params['right'] . '; ' .
            'UPDATE `tt_units` SET `lft` = `lft` - ' . $diff . ' ' .
            'WHERE `lft` > ' . $params['right'] . '; ' .
            'UPDATE `tt_units` SET `rgt` = `rgt` - ' . $diff . ' ' .
            'WHERE `rgt` > ' . $params['right'] . ';';
        $pdoState = $this->_dbObject->query($deleteSql);
        if ($pdoState === false) {
            return 0;
        } else {
            return 1;
        }
    }
}
