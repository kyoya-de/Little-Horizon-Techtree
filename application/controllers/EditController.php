<?php

class EditController extends Zend_Controller_Action
{

    public function init()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if (!$authSession->loggedIn) {
            $this->_response->setRedirect(
                $this->view->url(
                    array(
                        'controller' => 'objects',
                        'action' => 'details',
                        'id' => $this->_request->getParam('id', 'metalmine'),
                    ),
                    null,
                    true
                )
            );
        } else {
            if ($this->_request->isPost()) {
                TechTree_Log::log(
                    $authSession->id,
                    $this->_request->getParam('id'),
                    'edit ' . $this->_request->getActionName()
                );
            }
        }
    }
    
    public function commentAction()
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        $techtree = new Application_Model_TechTreeItems();
        $techtreeEdit = new Application_Model_TechTreeEdit();
        $comment = null;
        $this->view->doAction = null;
        if ($this->_request->isPost()) {
            $doAction = $this->_request->getParam('do', 'preview');
            $comment = $this->_request->getParam('comment', ':P');
            $this->view->doAction = $doAction;
            if ($doAction == 'save') {
                $this->view->saveResult = $techtreeEdit->setComment(
                    $this->_request->getParam('id'),
                    $comment
                );
            } else if ($doAction == 'delete') {
                TechTree_Log::log(
                    $authSession->id,
                    $this->_request->getParam('id'),
                    'delete comment'
                );
                $this->view->saveResult = $techtreeEdit->setComment(
                    $this->_request->getParam('id'),
                    ''
                );
                $comment = '';
            }
        }
        
        $this->view->item = $techtree->getItem(
            $this->_request->getParam('id', 'metalmine')
        );
        if ($comment === null) {
            $comment = $this->view->item['comment'];
        }
        $types = $techtree->getItemTypes();
        $objects = array();
        foreach ($types as $type) {
            $categories = $techtree->getTypeCategories($type['name']);
            foreach ($categories as $category) {
                $items = $techtree->getCategoryItems($category['name']);
                foreach ($items as $item) {
                    $objects[$type['dname']]
                        [$category['dname']]
                            [$item['name']] = $item['dname'];
                }
            }
        }
        $this->view->objects = $objects;
        $this->view->comment = $comment;
    }

    public function newlevelAction()
    {
        $techtree = new Application_Model_TechTreeItems();
        $this->view->types = $techtree->getItemTypes();
        $this->view->item = $techtree->getItem(
            $this->_request->getParam('id', 'metalmine')
        );
    }

    public function levelAction()
    {
        $techtree = new Application_Model_TechTreeItems();
        $this->view->item = $techtree->getItem(
            $this->_request->getParam('id')
        );
        $this->view->level = $this->_request->getParam('level');
        $this->view->types = $techtree->getItemTypes();
        $this->view->depencies = $techtree->getLevelDepencies(
            $this->_request->getParam('id'),
            $this->_request->getParam('level')
        );
        if ($this->_request->isPost()) {
            $requireList = explode(
                ',', $this->_request->getParam('requireList', '')
            );
            $requireData = array();
            foreach ($requireList as $requirement) {
                list($depId, $depLevel) = explode(':', $requirement);
                $requireData[] = array(
                    'depId' => $depId,
                    'depLevel' => $depLevel,
                );
            }
            $techtreeEdit = new Application_Model_TechTreeEdit();
            $techtreeEdit->editLevel(
                $this->_request->getParam('id'),
                $this->_request->getParam('level'),
                $requireData
            );
            $redirectUrl = $this->view->url(
                array(
                    'controller' => 'objects',
                    'action' => 'details',
                    'id' => $this->_request->getParam('id', 'metalmine'),
                ),
                null,
                true
            );
            $this->_response->setRedirect($redirectUrl);
        }
    }

    public function removeAction()
    {
        $techtreeEdit = new Application_Model_TechTreeEdit();
        $techtreeEdit->removeLevel(
            $this->_request->getParam('id'),
            $this->_request->getParam('level')
        );
        $this->_response->setRedirect(
            $this->view->url(
                array(
                    'controller' => 'objects',
                    'action' => 'details',
                    'id' => $this->_request->getParam('id'),
                ),
                null,
                true
            )
        );
    }
    
}
