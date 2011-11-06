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
 *
 * $Id: $
 */

/**
 * Class to manage bug tickets.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage Models
 */
class Application_Model_Bugs extends TechTree_Db_Model
{
    /**
     * Assign a bug ticket to a admin.
     *
     * @param int $bugId   ID of the bug to assign
     * @param int $adminId User ID of the admin to assign to
     *
     * @return void
     */
    public function assign($bugId, $adminId)
    {
        $bugSql = 'UPDATE `tt_bugs` SET `assignId` = ' .
            $this->_dbObject->quote($adminId) . ' WHERE `id` = ' .
            $this->_dbObject->quote($bugId);
        $this->_dbObject->query($bugSql);
    }

    /**
     * Sets the status of a bug ticket.
     *
     * @param int    $bugId  Id of the bug ticket
     * @param string $status New status of the bug ticket
     *
     * @return void
     */
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

    /**
     * Retrieves an array with all bug tickets.
     *
     * @return array
     */
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

    /**
     * Creates a new bug ticket.
     *
     * @param int    $userId  User Id of the ticket creator
     * @param string $summary Summary of the bug
     * @param string $details Detailed information about the bug
     *
     * @return void
     */
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

    /**
     * Retrieves all comments for the given bug.
     *
     * @param int $bugId ID of the bug ticket
     *
     * @return array
     */
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
