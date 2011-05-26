<?php

class AdminController extends Zend_Controller_Action
{

    public function preDispatch()
    {
        $auth = TechTree_Session::getNamespace('Auth');
        if (!$auth->loggedIn || $auth->accountType != 'admin') {
            $this->_response->setRedirect(
                $this->view->url(
                    array(
                        'controller' => 'index',
                        'action' => 'index',
                    )
                )
            );
        }
        
        $this->view->headTitle('Administration');
    }
    
    private function _doRedirect(array $urlParams)
    {
        $this->_response->setRedirect($this->view->url($urlParams, null, true));
    }

    public function userAction()
    {
        $userId = $this->_request->getParam('id', 1);
        $result = $this->_request->getParam('result');
        if ($result !== null) {
            $doAction = $this->_request->getParam('doAction');
            if ($doAction == 'userDelete') {
                $userId = 1;
            }
            $this->view->result = $result;
        }
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($userId == 1 && $authSession->id != $userId) {
            $userId = $authSession->id;
        }
        $admin = new Application_Model_Admin_Users();
        $this->view->userInfo = $admin->getUserInfo($userId);
    }

    public function objectAction()
    {
        $objectId = $this->_request->getParam('id', 'metalmine');
        $result = $this->_request->getParam('result');
        if ($result !== null) {
            $doAction = $this->_request->getParam('doAction');
            if ($doAction == 'objectDelete') {
                $objectId = 1;
            }
            $this->view->result = $result;
        }
        $admin = new Application_Model_Admin_Objects();
        $this->view->object = $admin->getObject($objectId);
    }

    public function doAction()
    {
        Application_Plugin_Referer::$saveReferer = false;
        $action = $this->_request->getParam('doAction');
        if ($action === null) {
            $this->_forward(
                'index',
                $this->_request->getControllerName(),
                $this->_request->getModuleName(),
                array()
            );
        }
        $referer = TechTree_Session::getNamespace('Referer');
        $admin = new Application_Model_Admin_Actions();
        $params = $referer->lastParams;
        $params['result'] = $admin->$action($this->_request->getParams());
        if (
            $referer->lastController != $this->_request->getControllerName() ||
            $referer->lastModule != $this->_request->getModuleName() ||
            $referer->lastAction != $this->_request->getActionName()
        ) {
            $urlParams = array(
                'controller' => $referer->lastController,
                'action' => $referer->lastAction,
                'module' => $referer->lastModule,
            );
            $urlParams += $params;
            $this->_doRedirect($urlParams);
        } else {
            $this->_doRedirect(
                array(
                    'controller' => $this->_request->getControllerName(),
                    'action' => 'index',
                    'module' => $this->_request->getModuleName(),
                )
            );
        }
    }

    public function categoryAction()
    {
        $categoryId = $this->_request->getParam('id', 1);
        $result = $this->_request->getParam('result');
        if ($result !== null) {
            $doAction = $this->_request->getParam('doAction');
            if ($doAction == 'categoryDelete') {
                $categoryId = 1;
            }
            $this->view->result = $result;
        }
        $admin = new Application_Model_Admin_Objects();
        $this->view->category = $admin->getCategory($categoryId);
    }

    public function usersAction()
    {
        $admin = new Application_Model_Admin_Users();
        $this->view->users = $admin->getUsers();
    }
    
    public function logAction()
    {
        $admin = new Application_Model_Admin_Log();
        $filter = array();
        if ($this->_request->isPost()) {
            $actionFilter = $this->_request->getParam('actionFilter', '');
            $objectFilter = $this->_request->getParam('objectFilter', '');
            $userFilter = $this->_request->getParam('userFilter', '');
            $dateFilter = $this->_request->getParam('dateFilter', '');
            if ($actionFilter != '') {
                $filter['log.`action`'] = $actionFilter;
            }
            if ($objectFilter != '') {
                $filter['log.`name`'] = $objectFilter;
            }
            if ($userFilter != '') {
                $filter['log.`userId`'] = $userFilter;
            }
            if ($dateFilter != '') {
                $filter['DATE(log.`timestamp`)'] = implode(
                    '-',
                    array_reverse(explode('.', $dateFilter))
                );
            }
        }
        $this->view->log = $admin->getLog($filter);
        $this->view->logActions = $admin->getLogActions($filter);
        $this->view->logObjects = $admin->getLogObjects($filter);
        $this->view->logUsers = $admin->getLogUsers($filter);
        $this->view->logDates = $admin->getLogDates($filter);
    }
    public function objectsAction()
    {
        $admin = new Application_Model_Admin_Objects();
        $this->view->tree = $admin->getCompleteTree();
    }
    
    public function clearlogAction()
    {
        $admin = new Application_Model_Admin_Log();
        $admin->clearLog();
        $this->_doRedirect(
            array(
                'controller' => 'admin',
                'action' => 'log',
            )
        );
    }
}
