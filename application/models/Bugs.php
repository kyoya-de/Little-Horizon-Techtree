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

    public function getBugs()
    {
        $bugsSql = "SELECT bug.`id`, bug.`title`, bug.`description`, bug.`status`, bug.`assignId`, reporter.`username`
                    FROM `tt_bugs` bug
                    LEFT JOIN `tt_users` reporter ON reporter.`id` = bug.`reporterId`
                    ORDER BY `priority` DESC, `id` DESC";
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
                'comments' => $this->getBugComments($row['id']),
            );
        }
        return $bugs;
    }

    public function reportBug($userId, $summary, $details)
    {
        $data = array(
            'USER_ID' => $userId,
            'TITLE' => $summary,
            'DESC' => $details,
        );
        $reportSql = "INSERT INTO `tt_bugs` (`reporterId`, `title`, `description`) VALUES (:USER_ID, :TITLE, :DESC)";
        $pdoState = $this->_dbObject->prepare($reportSql);
        $pdoState->execute($data);
    }

    public function getBugComments($bugId)
    {
        $pdoState = $this->_dbObject->query(
            "SELECT c.`message`, u.`username` FROM `tt_messages` c LEFT JOIN `tt_users` u ON u.`id` = c.`from`
            WHERE c.`to` = $bugId AND c.`type` = 'BUG_COMMENT' ORDER BY c.`timestamp`"
        );
        $comments = $pdoState->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }
}
