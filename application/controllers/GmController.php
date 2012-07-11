<?php

class GMController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'application/json', true);
    }

    public function utAction()
    {
        $userSettings = new Application_Model_UserSettings();
        $userSettings->parseUserTechs(
            $this->_request->getParam('userTechs', ''),
            $this->_request->getParam('techsId'),
            $this->_request->getParam('planet')
        );
    }

    public function dAction()
    {
        $user     = new Application_Model_Login();
        $username = $this->_request->getParam('u');
        $password = $this->_request->getParam('p');
        if ($user->login($username, $password)) {
            $userSettings     = new Application_Model_UserSettings();
            $authSession      = TechTree_Session::getNamespace('Auth');
            $planets          = $userSettings->getRegisteredPlanets(
                $authSession->techsId
            );
            $this->view->json = json_encode($planets);
            $user->logout();
        }
    }
}
