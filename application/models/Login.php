<?php

class Application_Model_Login
{
    public function login($username, $password, $stayLoggedIn = 0)
    {
        /*
         * New login routine.
         */
        $authAdapter = new TechTree_Auth_Adapter_PDO(
            TechTree_Db_Model::getDefaultDbConnection(),
            array(
                'tableName'        => 'tt_users',
                'identityField'    => 'username',
                'credentialField'  => 'password',
                'passwordFunction' => 'MD5(?)',
                'customConditon'   => '`active` = 1',
            )
        );
        
        $authAdapter->setIdentity($username, $password);
        
        $auth = Zend_Auth::getInstance();
        $authResult = $auth->authenticate($authAdapter);
        if ($authResult->isValid()) {
            $resultRow = $authResult->getIdentity();
            $authStorage = TechTree_Session::getNamespace('Auth');
            $authStorage->loggedIn = true;
            $authStorage->id = $resultRow['id'];
            $authStorage->username = $resultRow['username'];
            $authStorage->techsId = $resultRow['techs_id'];
            $authStorage->accountType = $resultRow['account_type'];
            TechTree_Log::log($resultRow['id'], 'techtree', 'login');
            if ($resultRow['current_planet'] == '') {
                $userSettings = new Application_Model_UserSettings();
                $planets = $userSettings->getRegisteredPlanets(
                    $resultRow['techs_id']
                );
                $userSettings->setNewPlanet($resultRow->id, $planets[0]);
            }
            $authStorage->currentPlanet = $resultRow['current_planet'];
            $authStorage->style = $resultRow['style'];
            if ($stayLoggedIn == 1) {
                $crypt = TechTree_Crypt::getInstance('lh_techtree');
                $ident = $authAdapter->getIdentity();
                $identity = $crypt->encrypt(
                    $ident['identity'] . ':' . $ident['credential']
                );
                $frontController = Zend_Controller_Front::getInstance();
                $request = $frontController->getRequest();
                setcookie(
                    'tt_autologin',
                    $identity . ':' . md5($identity),
                    time() + 365 * 24 * 60 * 60,
                    $frontController->getBaseUrl() . '/',
                    $request->get('SERVER_NAME')
                );
            }
            return true;
        }
        
        return false;
    }

    public function logout()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        TechTree_Log::log($authSession->id, 'techtree', 'logout');
        TechTree_Session::clearAll(false);
        $authSession->loggedIn = false;
        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        if ($request->get('tt_autologin') !== null) {
            setcookie(
                'tt_autologin',
                null,
                null,
                $frontController->getBaseUrl() . '/',
                $request->get('SERVER_NAME')
            );
        }
    }
}

