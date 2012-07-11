<?php

class NewController extends Zend_Controller_Action
{

    public function init()
    {
        $techtree          = new Application_Model_TechTreeItems();
        $this->view->types = $techtree->getItemTypes();
    }

    private function _doRedirect($url)
    {
        if (is_array($url)) {
            $url = $this->view->url($url);
        }
        $this->_response->setRedirect($url);
    }

    public function objectAction()
    {
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            if ($params['objectName'] != '' && $params['objectId'] != '') {
                $techtreeEdit = new Application_Model_TechTreeEdit();
                $techtreeEdit->createObject(
                    $params['objectId'],
                    $params['objectName'],
                    $params['race'],
                    $params['insertBefore']
                );
                $authSession = TechTree_Session::getNamespace('Auth');
                TechTree_Log::log(
                    $authSession->id,
                    $params['objectId'],
                    'create object'
                );
            }
            $this->_doRedirect(
                array(
                    'controller' => 'objects',
                    'action'     => $params['type'],
                    'category'   => $params['category']
                )
            );
        }
    }

    public function categoryAction()
    {
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            if ($params['categoryName'] != '') {
                $techtreeEdit = new Application_Model_TechTreeEdit();
                $categoryId   = md5(microtime());
                $techtreeEdit->createCategory(
                    $categoryId,
                    $params['categoryName'],
                    $params['insertBefore']
                );
                $authSession = TechTree_Session::getNamespace('Auth');
                TechTree_Log::log(
                    $authSession->id,
                    $categoryId,
                    'create category'
                );
            }
            $this->_doRedirect(
                array(
                    'controller' => 'objects',
                    'action'     => $params['type']
                )
            );
        }
    }
}





