<?php

class Application_Model_Bugs extends TechTree_Db_Model
{
    public function assign($bugId, $adminId)
    {
        $bugSql = 'UPDATE `tt_bugs` SET `assignId` = ' .
            $this->_dbObject->quote($adminId) . ' WHERE `id` = ' .
            $this->_dbObject->quote($bugId);
        $this->_dbObject->query($bugSql);
    }
    public function setState($bugId, $status)
    {
        $priority = 1;
        if ($status == 'closed' || $status == 'resolved') {
            $priority = 0;
        }
        $bugSql = 'UPDATE `tt_bugs` SET `status` = ' .
            $this->_dbObject->quote($status) . ', `priority` = ' .
            $priority . ' WHERE `id` = ' . $this->_dbObject->quote($bugId);
        $this->_dbObject->query($bugSql);
    }
}
