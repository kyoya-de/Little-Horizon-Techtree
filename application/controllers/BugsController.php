<?php

class BugsController extends Zend_Controller_Action
{
    public function init()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if (!$authSession->loggedIn) {
            $this->_doRedirect(
                array('controller' => 'index', 'action' => 'index')
            );
        }
    }

    private function _doRedirect($url)
    {
        if (is_array($url)) {
            $url = $this->view->url($url, null, true);
        }
        
        $this->_response->setRedirect($url);
    }

    public function indexAction()
    {
        $bugs = TechTree_Bugs::getInstance();
        $users = new Application_Model_Users();
        $this->view->admins = $users->getAdmins();
        $this->view->states = $users->getStates();
        $this->view->bugs = $bugs->getBugs();
    }

    public function assignAction()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($authSession->loggedIn && $authSession->accountType == 'admin') {
            $bugs = new Application_Model_Bugs();
            $bugs->assign(
                $this->_request->getParam('bugId'),
                $this->_request->getParam('adminId')
            );
        }
        $this->_doRedirect(
            array(
                'controller' => 'bugs',
                'action' => 'index',
            )
        );
    }

    public function statusAction()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($authSession->loggedIn && $authSession->accountType == 'admin') {
            $bugs = new Application_Model_Bugs();
            $bugs->setState(
                $this->_request->getParam('bugId'),
                $this->_request->getParam('status')
            );
        }
        $this->_doRedirect(
            array(
                'controller' => 'bugs',
                'action' => 'index',
            )
        );
    }

    public function reportAction()
    {
        $bugReportForm = new Application_Form_ReportBug();
        $bugReportForm->removeDecorator('HtmlTag');
        $bugReportForm->setAction(
            $this->view->url(
                array(
                    'controller' => 'bugs',
                    'action' => 'report',
                )
            )
        );
        if ($this->_request->isPost()) {
            if ($bugReportForm->isValid($this->_request->getParams())) {
                $bugs = TechTree_Bugs::getInstance();
                $authSession = TechTree_Session::getNamespace('Auth');
                $bugs->reportBug(
                    $authSession->id,
                    $bugReportForm->getValue('summary'),
                    $bugReportForm->getValue('details')
                );
                $this->_doRedirect(
                    array('controller' => 'bugs', 'action' => 'index')
                );
            }
        }
        $this->view->form = $bugReportForm;
    }
}
