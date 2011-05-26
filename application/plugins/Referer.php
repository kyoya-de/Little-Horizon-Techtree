<?php
class Application_Plugin_Referer extends Zend_Controller_Plugin_Abstract
{
    public static $saveReferer = true;
    public function dispatchLoopShutdown()
    {
        if (!self::$saveReferer) {
            return;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $refSession = TechTree_Session::getNamespace('Referer');
        $requestParams = $request->getParams();
        $refSession->lastController = $request->getControllerName();
        $refSession->lastAction = $request->getActionName();
        $refSession->lastModule = $request->getModuleName();
        unset(
            $requestParams['module'],
            $requestParams['controller'],
            $requestParams['action']
        );
        $refSession->lastParams = $requestParams;
    }
}
