<?php
class UserController extends Zend_Controller_Action
{
    public function loginAction()
    {
        $loginForm = new Application_Form_Login();
        $loginForm->removeDecorator('HtmlTag');
        if ($this->_request->isPost()) {
            if ($loginForm->isValid($this->_request->getParams())) {
                $login = new Application_Model_Login();
                $loginResult = $login->login(
                    $loginForm->getValue('username'), 
                    $loginForm->getValue('password'),
                    $loginForm->getValue('stayLoggedIn')
                );
                if ($loginResult) {
                    $this->_response->setRedirect(
                        $this->view->url(
                            array(
                                'controller' => 'index', 
                                'action' => 'index'
                            ), 
                            null, 
                            true
                        )
                    );
                }
            }
        }
        $this->view->loginForm = $loginForm;
    }
    
    public function logoutAction()
    {
        $login = new Application_Model_Login();
        $login->logout();
        $this->_response->setRedirect(
            $this->view->url(
                array(
                    'controller' => 'index', 
                    'action' => 'index'
                ),
                null,
                true
            )
        );
    }
    
    public function registerAction()
    {
        $registerSuccessful = 'Dein Account wurde registriert. '
                            . 'Du kannst dich jetzt einloggen.';
        $registerForm = new Application_Form_Register();
        $registerForm->removeDecorator('HtmlTag');
        if ($this->_request->isPost()) {
            if ($registerForm->isValid($this->_request->getParams())) {
                $register = new Application_Model_Register();
                $register->registerUser($registerForm->getValues());
                $this->view->registerMessage = $registerSuccessful;
            }
        }
        $this->view->registerForm = $registerForm;
    }
    
    public function settingsAction()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        $userSettings = new Application_Model_UserSettings();
        $basicSetForm = new Application_Form_UserBasicSettings();
        $basicSetForm->removeDecorator('HtmlTag');
        $userTechsForm = new Application_Form_UserTechSettings();
        $userTechsForm->removeDecorator('HtmlTag');
        $passwordForm = new Application_Form_Password();
        $passwordForm->removeDecorator('HtmlTag');
        $setType = $this->_request->getParam('settings_type', '');
        if ($setType == 'basic') {
            if ($basicSetForm->isValid($this->_request->getParams())) {
                $userSettings->setBasicSettings(
                    $basicSetForm->getValue('user_id'), 
                    array(
                        'style' => $basicSetForm->getValue('style'), 
                        'techs_id' => $basicSetForm->getValue('techs_id'),
                    )
                );
            }
        } elseif ($setType == 'usertechs') {
            $objects = $userSettings->parseUserTechs(
                $this->_request->getParam('user_techs')
            );
            $this->view->parsedUserTechs = $objects;
        } elseif ($setType == 'setPassword') {
            if ($passwordForm->isValid($this->_request->getParams())) {
                $userSettings->setPassword(
                    $passwordForm->getValue('password')
                );
            }
        } elseif ($setType == 'managePlanet') {
            if ($this->_request->getParam('deletePlanet', 0) == 1) {
                $userSettings->deletePlanet(
                    $this->_request->getParam('planet')
                );
                if ($authSession->currentPlanet ==
                    $this->_request->getParam('planet')) {
                    $planets = $userSettings->getRegisteredPlanets();
                    $planets = array_keys($planets);
                    $authSession->currentPlanet = $planets[0];
                    $userSettings->setNewPlanet(
                        $authSession->id,
                        $planets[0]
                    );
                }
            } else {
                $userSettings->createPlanet(
                    $this->_request->getParam('newPlanet', 'default')
                );
                $userSettings->setNewPlanet(
                    $authSession->id,
                    $this->_request->getParam('newPlanet', 'default')
                );
                $authSession->currentPlanet = $this->_request->getParam(
                    'newPlanet',
                    'default'
                ); 
            }
        }
        $this->view->userSettings = $userSettings->getSettings(
            $authSession->id
        );
        $basicSetForm->setDefaults(
            $this->view->userSettings['basicSettings']
        );
        $basicSetForm->setDefault('user_id', $authSession->id);
        $managePlanetsForm = new Application_Form_ManagePlanets();
        $managePlanetsForm->removeDecorator('HtmlTag');
        $this->view->basicSettingsForm = $basicSetForm;
        $this->view->managePlanetsForm = $managePlanetsForm;
        $this->view->userTechsForm = $userTechsForm;
        $this->view->passwordForm = $passwordForm;
    }
    public function setPlanetAction()
    {
        $newPlanet = $this->_request->getParam('planet');
        if ($newPlanet !== null) {
            $authSession = TechTree_Session::getNamespace('Auth');
            $authSession->currentPlanet = $newPlanet;
            $userSettings = new Application_Model_UserSettings();
            $userSettings->setNewPlanet($authSession->id, $newPlanet);
        }
        $currentParams = $this->_request->getParams();
        $redirectParams = array();
        foreach ($currentParams as $key => $value) {
            if (substr($key, 0, 5) !== 'last-') {
                continue;
            }
            $newKey = substr($key, 5, strlen($key) - 5);
            $redirectParams[$newKey] = $value;
        }
        $this->_response->setRedirect(
            $this->view->url(
                $redirectParams,
                null,
                true
            )
        );
    }
    public function deletetechsAction()
    {
        $userSettings = new Application_Model_UserSettings();
        $authSession = TechTree_Session::getNamespace('Auth');
        $planetId = $authSession->currentPlanet;
        if ($this->_request->getParam('deletionType', 'planet') == 'research') {
            $planetId = 'Forschungen';
        }

        $userSettings->deleteUserTechs(
            $authSession->techsId,
            $planetId,
            $this->_request->getParam('techId', array())
        );
        $this->_redirect('user/settings');
    }
}
