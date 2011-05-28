<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 28.05.11
 * Time: 12:17
 * To change this template use File | Settings | File Templates.
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
