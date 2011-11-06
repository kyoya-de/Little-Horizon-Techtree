<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('TechTree');
    }

    protected function _initDb()
    {
        $options = $this->getOption('resources');
        $dbObject = new TechTree_Db($options['db']);
        TechTree_Db_Model::setDefaultDbConnection($dbObject);
    }
    
    protected function _initView()
    {
        $view = new Zend_View();
        $view->addHelperPath(
            'ZendX/JQuery/View/Helper/',
            'ZendX_JQuery_View_Helper'
        );
        $view->doctype(Zend_View_Helper_Doctype::XHTML1_TRANSITIONAL);

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        return $view;
    }
}

