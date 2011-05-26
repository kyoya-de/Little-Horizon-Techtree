<?php
class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    public function getcategoriesAction()
    {
        $type = $this->_request->getParam('type', 'building');
        $techtree = new Application_Model_TechTreeItems();
        $this->view->objects = $techtree->getTypeCategories($type);
        $this->render('getAjax');
        
    }
    public function getnewcategoriesAction()
    {
        $type = $this->_request->getParam('type', 'building');
        $techtree = new Application_Model_TechTreeItems();
        $this->view->objects = $techtree->getTypeCategories($type);
        $this->view->id = $type;
        $this->render('getNewAjax');
        
    }
    public function getobjectsAction()
    {
        $category = $this->_request->getParam('category', 'bui_01');
        $techtree = new Application_Model_TechTreeItems();
        $this->view->objects = $techtree->getCategoryItems($category);
        $this->render('getAjax');
    }
    public function getnewobjectsAction()
    {
        $category = $this->_request->getParam('category', 'bui_01');
        $techtree = new Application_Model_TechTreeItems();
        $this->view->objects = $techtree->getCategoryItems($category);
        $this->view->id = $category;
        $this->render('getNewAjax');
    }
    
    public function parseAction()
    {
        $techtreeEdit = new Application_Model_TechTreeEdit();
        $source = $this->_request->getParam('data', '');
        $objectId = $this->_request->getParam('id');
        if (strpos($source, '---') !== false) {
            $techtreeEdit->parseMultipleData($objectId, $source);
        } else {
            $techtreeEdit->parseData(
                $objectId,
                $this->_request->getParam('level'),
                $source
            );
        }
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->view->id = $objectId;
    }
}
