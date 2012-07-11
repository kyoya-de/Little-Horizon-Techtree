<?php
class Application_Model_TechTreeEdit extends TechTree_Db_Model
{
    public function setComment($objectId, $comment)
    {
        $updateSql = 'UPDATE `tt_units` SET `comment` = ' .
                     $this->_dbObject->quote($comment) . ' WHERE `name` = ' .
                     $this->_dbObject->quote($objectId);
        $pdoState  = $this->_dbObject->query($updateSql);
        if ($pdoState === false) {
            return false;
        }

        return ($pdoState->rowCount() == 0) ? false : true;
    }

    public function editLevel($objectId, $newLevel, $levelData)
    {
        $this->removeLevel($objectId, $newLevel);
        $deleteSql = 'DELETE FROM `tt_depencies` WHERE `id` = ' .
                     $this->_dbObject->quote($objectId) . ' AND `level` >= ' .
                     $this->_dbObject->quote($newLevel) . ' AND `depid` = :depId AND ' .
                     '`deplevel` = :depLevel';
        $checkSql  = 'SELECT COUNT(`key`) items FROM `tt_depencies` WHERE ' .
                     '`id` = ' . $this->_dbObject->quote($objectId) . ' AND ' .
                     '`level` <= ' . $this->_dbObject->quote($newLevel) . ' AND ' .
                     '`depid` = :depId AND `deplevel` = :depLevel ';
        $insertSql = 'INSERT INTO `tt_depencies` (`id`, `level`, `depid`, ' .
                     '`deplevel`) VALUES (' . $this->_dbObject->quote($objectId) .
                     ', ' . $this->_dbObject->quote($newLevel) . ', :depId, :depLevel)';
        $delState  = $this->_dbObject->prepare($deleteSql);
        $chkState  = $this->_dbObject->prepare($checkSql);
        $insState  = $this->_dbObject->prepare($insertSql);

        foreach ($levelData as $data) {
            $delState->execute($data);
            $chkState->execute($data);
            $row = $chkState->fetch(PDO::FETCH_ASSOC);
            if ($row['items'] == 0) {
                $insState->execute($data);
                $insState->closeCursor();
            }
            $delState->closeCursor();
            $chkState->closeCursor();
        }
    }

    public function removeLevel($objectId, $level)
    {
        $deleteSql = 'DELETE FROM `tt_depencies` WHERE `id` = ' .
                     $this->_dbObject->quote($objectId) . ' AND `level` = ' .
                     $this->_dbObject->quote($level);
        $this->_dbObject->query($deleteSql);
    }

    public function parseData($objectId, $level, $source)
    {
        $source     = str_replace(
            array("\r", "\t", "  "),
            array("", " ", " "),
            $source
        );
        $items      = array();
        $pregResult = preg_match_all(
            '/([A-Z]+) ([0-9]+)\\/[0-9]/',
            $source,
            $matches,
            PREG_SET_ORDER
        );
        if ($pregResult) {
            foreach ($matches as $match) {
                $items[] = array(
                    'depId'    => $match[1],
                    'depLevel' => $match[2],
                );
            }
            $this->editLevel($objectId, $level, $items);
        }
    }

    public function parseMultipleData($objectId, $source)
    {
        $source = str_replace(
            array("\r", "\t", "  "),
            array("", " ", " "),
            $source
        );
        preg_match_all(
            '/---(\d+)---' . "\n" . '([^-]+)/si',
            $source,
            $splitted,
            PREG_SET_ORDER
        );
        foreach ($splitted as $data) {
            $this->parseData($objectId, $data[1], $data[2]);
        }
    }

    public function createCategory($categoryId, $name, $insertBefore)
    {
        $beforeSql = 'SELECT `rgt` FROM `tt_units` WHERE `name` = ' .
                     $this->_dbObject->quote($insertBefore);
        $pdoState  = $this->_dbObject->query($beforeSql);
        $row       = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        $rgt            = $row['rgt'];
        $newCategorySql = 'UPDATE `tt_units` SET `rgt` = `rgt` + 2 WHERE ' .
                          '`rgt` >= ' . $rgt . '; UPDATE `tt_units` SET `lft` = `lft` + 2 ' .
                          'WHERE `lft` > ' . $rgt . '; INSERT INTO `tt_units` (`name`, ' .
                          '`dname`, `race`, `lft`, `rgt`, `type`) VALUES (:id, :name, ' .
                          '\'normal\', :lft, :rgt, \'category\');';
        $pdoState       = $this->_dbObject->prepare($newCategorySql);
        $pdoState->execute(
            array(
                'id'   => $categoryId,
                'name' => $name,
                'lft'  => $rgt,
                'rgt'  => $rgt + 1,
            )
        );
    }

    public function createObject($objectId, $objectName, $race, $insertBefore)
    {
        $beforeSql = 'SELECT `rgt` FROM `tt_units` WHERE `name` = ' .
                     $this->_dbObject->quote($insertBefore);
        $pdoState  = $this->_dbObject->query($beforeSql);
        $row       = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        $rgt            = $row['rgt'];
        $newCategorySql = 'UPDATE `tt_units` SET `rgt` = `rgt` + 2 WHERE ' .
                          '`rgt` >= ' . $rgt . '; UPDATE `tt_units` SET `lft` = `lft` + 2 ' .
                          'WHERE `lft` > ' . $rgt . '; INSERT INTO `tt_units` (`name`, ' .
                          '`dname`, `race`, `lft`, `rgt`, `type`) VALUES (:id, :name, ' .
                          ':race, :lft, :rgt, \'item\');';
        $pdoState       = $this->_dbObject->prepare($newCategorySql);
        $pdoState->execute(
            array(
                'id'   => $objectId,
                'name' => $objectName,
                'race' => $race,
                'lft'  => $rgt,
                'rgt'  => $rgt + 1,
            )
        );
    }
}

