<?php

class Application_Model_TechTreeItems extends TechTree_Db_Model
{
    public function getItemTypes()
    {
        $typesSql = 'SELECT `id`, `name`, `dname`, `lft`, `rgt`, ' .
            '`last_update` FROM `tt_units` WHERE `type` = \'type\'';
        $result = $this->_dbObject->query($typesSql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypeCategories($type)
    {
        $typeSql = 'SELECT `lft` \'LEFT\', `rgt` \'RIGHT\' FROM `tt_units` ' .
            'WHERE `name` = ' . $this->_dbObject->quote($type);
        $result = $this->_dbObject->query($typeSql);
        $type = $result->fetch(PDO::FETCH_ASSOC);
        
        $categoriesSql = 'SELECT `id`, `name`, `dname`, `lft`, `rgt`, ' .
            '`last_update` FROM `tt_units` WHERE `type` = \'category\' AND ' .
            '`lft` > :LEFT AND `rgt` < :RIGHT';
        $pdoStatement = $this->_dbObject->prepare($categoriesSql);
        $pdoStatement->execute($type);
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryItems($category)
    {
        $categorySql = 'SELECT `lft` \'LEFT\', `rgt` \'RIGHT\' ' .
            'FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($category);
        $result = $this->_dbObject->query($categorySql);
        $category = $result->fetch(PDO::FETCH_ASSOC);
        
        $itemsSql = 'SELECT `id`, `name`, `dname`, `last_update`, ' .
            '`max_level`, `race` FROM `tt_units` ' .
            'WHERE `type` = \'item\' AND `lft` > :LEFT AND `rgt` < :RIGHT';
        $pdoStatement = $this->_dbObject->prepare($itemsSql);
        $pdoStatement->execute($category);
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryName($category)
    {
        $categorySql = 'SELECT `dname` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($category);
        $result = $this->_dbObject->query($categorySql);
        $categoryEntry = $result->fetch(PDO::FETCH_ASSOC);
        return $categoryEntry['dname'];
    }

    public function getTypeItems($type)
    {
        $typeCategories = $this->getTypeCategories($type);
        $typeItems = array();
        foreach ($typeCategories as $typeCategory) {
            $categoryItems = $this->getCategoryItems($typeCategory['name']);
            $typeItems[$typeCategory['dname']] = $categoryItems;
        }
        return $typeItems;
    }

    public function getItem($name)
    {
        $itemSql = 'SELECT `id`, `name`, `dname`, `last_update`, ' .
            '`max_level`, `race`, `comment` FROM `tt_units` ' .
            'WHERE `name` = ' . $this->_dbObject->quote($name);
        $result = $this->_dbObject->query($itemSql);
        $object = $result->fetch(PDO::FETCH_ASSOC);
        $object['levels'] = $this->_getItemLevels($object['name']);
        return $object;
    }

    public function getItemPath($itemName)
    {
        $itemSql = 'SELECT `lft` \'LEFT\', `rgt` \'RIGHT\' FROM `tt_units` ' .
            'WHERE `name` = ' . $this->_dbObject->quote($itemName);
        $result = $this->_dbObject->query($itemSql);
        $item = $result->fetch(PDO::FETCH_ASSOC);
        $result->closeCursor();
        
        $pathSql = 'SELECT `name`, `dname`, `type` FROM `tt_units` ' .
            'WHERE `lft` < :LEFT AND `rgt` > :RIGHT';
        
        $pdoStatement = $this->_dbObject->prepare($pathSql);
        $pdoStatement->execute($item);
        $path = array();
        while (false !== ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC))) {
            $path[$row['type']] = array(
                'name' => $row['name'],
                'dname' => $row['dname'],
            );
        }
        return $path;
    }
    
    public function getItemDepencies($itemName)
    {
        $depenciesSql = 'SELECT depency.`level`, depency.`depid`, ' .
            'depency.`deplevel`, units.`dname` FROM `tt_depencies` depency ' .
            'LEFT JOIN `tt_units` units ON units.`name` = depency.`depid` ' .
            'WHERE depency.`id` = :ID ORDER BY units.`dname`';
        $pdoStatement = $this->_dbObject->prepare($depenciesSql);
        
        $pdoResult = $pdoStatement->execute(array('ID' => $itemName));
        
        if (!$pdoResult) {
            return array();
        }
        $result = array();
        while (false !== ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC))) {
            $itemPath = $this->getItemPath($row['depid']);
            $result[$row['level']][$itemPath['type']['dname']][$row['depid']] =
                array(
                    'level' => $row['deplevel'],
                    'dname' => $row['dname'],
                );
        }
        
        return $result;
    }
    
    public function search($searchTerm)
    {
        $searchTerm = '%' . $searchTerm . '%';
        $searchSql = 'SELECT `name`, `dname` FROM `tt_units` WHERE ' .
            '(`name` LIKE :SEARCH_TERM OR `dname` LIKE :SEARCH_TERM) AND ' .
            '`rgt` = `lft` + 1';
        $pdoStatement = $this->_dbObject->prepare($searchSql);
        $pdoResult = $pdoStatement->execute(
            array('SEARCH_TERM' => $searchTerm)
        );
        
        $searchResult = array();
        
        if (!$pdoResult) {
            return $searchResult;
        }
        while (false !== ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC))) {
            $searchResult[$row['name']] = $row['dname'];
        }
        
        return $searchResult;
    }
    
    private function _getSubStatistics($parentName, $type)
    {
        $parentSql = 'SELECT `lft`, `rgt` FROM `tt_units` WHERE `name` = ' .
            $this->_dbObject->quote($parentName);
        $pdoState = $this->_dbObject->query($parentSql);
        $row = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        
        $childsSql = 'SELECT `name`, `dname`, `lft`, `rgt` ' .
            'FROM `tt_units` WHERE `type` = ' . $this->_dbObject->quote($type) .
            ' AND `lft` > ' . $row['lft'] . ' AND `rgt` < ' . $row['rgt'];
        
        $itemsSql = 'SELECT COUNT(`id`) childs FROM `tt_units` ' .
            'WHERE `rgt` = `lft` + 1 AND `lft` > :lft AND `rgt` < :rgt';
        $pdoState = $this->_dbObject->query($childsSql);
        $pdoStateItems = $this->_dbObject->prepare($itemsSql);
        $statistic = array();
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $pdoStateItems->execute(
                array(
                    'lft' => $row['lft'],
                    'rgt' => $row['rgt'],
                )
            );
            $childCount = $pdoStateItems->fetch(PDO::FETCH_ASSOC);
            $pdoStateItems->closeCursor();
            if ($type != 'category') {
                $childs = $this->_getSubStatistics($row['name'], 'category');
                $statistic[$row['name']] = array(
                    'dname' => $row['dname'],
                    'count' => $childCount['childs'],
                    'childs' => $childs,
                );
            } else {
                $statistic[$row['name']] = array(
                    'dname' => $row['dname'],
                    'count' => $childCount['childs'],
                );
            }
        }
        return $statistic;
    }

    public function getStatistics()
    {
        $statistic = array();
        $rootSql = 'SELECT `name`, `dname` ' .
            'FROM `tt_units` WHERE `type` = \'root\' ORDER BY `lft`';
        $itemsSql = 'SELECT COUNT(`id`) childs FROM `tt_units` ' .
            'WHERE `rgt` = `lft` + 1';
        $pdoResult = $this->_dbObject->query($rootSql);
        
        $root = $pdoResult->fetch(PDO::FETCH_ASSOC);
        $pdoResult->closeCursor();
        $pdoResult = $pdoResult = $this->_dbObject->query($itemsSql);
        $childCount = $pdoResult->fetch(PDO::FETCH_ASSOC);
        $pdoResult->closeCursor();
        $childs = $this->_getSubStatistics($root['name'], 'type');
        $statistic[$root['name']] = array(
            'dname' => $root['dname'],
            'count' => $childCount['childs'],
            'childs' => $childs,
        );
        return $statistic;
    }
    
    public function getReverseDepencies($name)
    {
        $reverseSql = 'SELECT rev.`id`, rev.`level`, unit.`dname`, ' .
            'rev.`deplevel` FROM `tt_depencies` rev LEFT JOIN ' .
            '`tt_units` unit ON unit.`name` = rev.`id` WHERE rev.`depid` = ' .
            $this->_dbObject->quote($name);
        $pdoState = $this->_dbObject->query($reverseSql);
        $result = array();
        if ($pdoState === false) {
            return $result;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $itemPath = $this->getItemPath($row['id']);
            $result[$row['deplevel']][$itemPath['type']['dname']][$row['id']] =
                array(
                    'dname' => $row['dname'],
                    'level' => $row['level'],
                );
            krsort($result[$row['deplevel']]);
        }
        ksort($result);
        return $result;
    }
    
    public function getLevelDepencies($objectId, $level)
    {
        $depenciesSql = 'SELECT dep.`depid`, dep.`deplevel`, unit.`dname` ' .
            'FROM `tt_depencies` dep LEFT JOIN `tt_units` unit ON ' .
            'unit.`name` = dep.`depid` WHERE dep.`level` = \'' . $level .
            '\' AND dep.`id` = \'' . $objectId . '\'';
        $pdoState = $this->_dbObject->query($depenciesSql);
        $depencies = array();
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $depencies[$row['depid']] = array(
                'dname' => $row['dname'],
                'level' => $row['deplevel'],
            );
        }
        
        return $depencies;
    }
    
    private function _getItemLevels($objectName)
    {
        $maxLevel = $this->_getMaxLevel($objectName);
        $levelSql = 'SELECT DISTINCT `level` FROM `tt_depencies` WHERE ' .
            '`id` = ' . $this->_dbObject->quote($objectName) . ' ' .
            'ORDER BY `level`';
        $pdoState = $this->_dbObject->query($levelSql);
        $levels = array();
        
        if ($pdoState === false) {
            return $levels;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $levels[] = $row['level'];
        }
        
        $levelCount = count($levels);
        $newLevels = array();
        if ($levelCount == 1) {
            return array(1 => -1);
        }
        foreach ($levels as $key => $level) {
            if (($key + 1) >= $levelCount && $level < $maxLevel) {
                $newLevels[$level] = 0;
            } else if (
                ($level >= $maxLevel) ||
                ($levels[$key + 1] == ($level + 1))) {
                $newLevels[$level] = -1;
            } else {
                $newLevels[$level] = $levels[$key + 1] - 1;
            }
        }
        
        return $newLevels;
    }
    
    private function _getMaxLevel($objectName)
    {
        $maxLevelSql = 'SELECT `max_level` maxLevel FROM `tt_units` WHERE ' .
            '`name` = ' . $this->_dbObject->quote($objectName);
        $pdoState = $this->_dbObject->query($maxLevelSql);
        $row = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        return $row['maxLevel'];
    }
}

