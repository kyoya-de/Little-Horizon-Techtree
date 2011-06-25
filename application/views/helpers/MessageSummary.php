<?php
/**
 * This file is a part of the Little Horizon Community TechTree project.
 * The whole project is licensed under the terms of the MIT license.
 * Take a look at the LICENSE file for information your rights.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage View_Helpers
 * @version    4.1.2
 * @author     Stefan Krenz
 */

/**
 * This view helper builds the message summary for the user.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage View_Helpers
 */
class Zend_View_Helper_MessageSummary extends Zend_View_Helper_Abstract
{
    public function messageSummary()
    {
        $auth = TechTree_Session::getNamespace('Auth');
        if (!$auth->loggedIn) {
            return '';
        }
        $msgModel = new Application_Model_Messages();
        $msgSummary = $msgModel->getMessageSummary($auth->id);
        $view = clone $this->view;
        $view->clearVars();
        $view->assign($msgSummary);
        $msgSession = TechTree_Session::getNamespace('Messages');
        if ($msgSummary['msgNew'] > $msgSession->new) {
            $view->assign('newMsgCount', $msgSummary['msgNew'] - $msgSession->new);
            $msgSession->new = $msgSummary['msgNew'];
        }
        $userModel = new Application_Model_Users();
        $view->assign('users', $userModel->getActiveUsers($auth->id));
        return $view->render('common/messages.phtml');
    }
}
