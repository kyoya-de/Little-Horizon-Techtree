<?php
class Application_Plugin_AutoLogin extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($authSession->loggedIn) {
            return;
        }
        
        $cookie = $request->get('tt_autologin');
        if ($cookie === null || $cookie == '') {
            return;
        }
        list($authInfo, $checksum) = explode(':', $cookie);
        if (md5($authInfo) != $checksum) {
            return;
        }
        
        $crypt = TechTree_Crypt::getInstance('lh_techtree');
        list($user, $pass) = explode(':', $crypt->decrypt($authInfo));
        $login = new Application_Model_Login();
        $login->login($user, $pass, 1);
    }
}
