<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 27.05.11
 * Time: 19:11
 * To change this template use File | Settings | File Templates.
 */
 
class MessageController extends Zend_Controller_Action
{
    private $_auth = null;
    public function preDispatch()
    {
        $this->_auth = TechTree_Session::getNamespace('Auth');
        if (!$this->_auth->loggedIn) {
            $this->_response->setRedirect(
                $this->view->url(
                    array('controller' => 'index', 'action' => 'index'),
                    null,
                    true
                )
            );
        }
        if (substr($this->_request->getActionName(), 0, 4) == ajax) {
            Zend_Layout::getMvcInstance()->disableLayout();
            $this->_helper->viewRenderer('common/ajax', null, true);
            $this->_response->setHeader('Content-Type', 'text/plain; charset=utf-8');
        }
    }

    public function indexAction()
    {
        $msgModel = new Application_Model_Messages();
        $messages = $msgModel->getMessageOverview($this->_auth->id);
        $this->view->messages = $messages;
    }

    public function ajaxGetMessageAction()
    {
        $msgModel = new Application_Model_Messages();
        $message = $msgModel->getMessage($this->_request->getParam('id'), $this->_auth->id);
        $this->view->ajaxResponse = $this->view->escape($message);
        $this->_response->setHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function ajaxSendReplyAction()
    {
        $this->view->ajaxResponse = true;
        $this->_response->setHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
