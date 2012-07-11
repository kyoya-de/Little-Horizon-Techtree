<?php

class ObjectsController extends Zend_Controller_Action
{
    public function buildingAction()
    {
        $this->view->user = new Application_Model_UserSettings();
        $techtree         = new Application_Model_TechTreeItems();
        $selectedCategory = $this->_request->getParam('category');
        if ($selectedCategory !== null && $selectedCategory != '') {
            $categoryName          = $techtree->getCategoryName(
                $selectedCategory
            );
            $this->view->typeItems = array(
                $categoryName => $techtree->getCategoryItems(
                    $selectedCategory
                )
            );
        } else {
            $this->view->typeItems = $techtree->getTypeItems(
                $this->_request->getParam('action')
            );
        }

        $this->view->typeCategories = $techtree->getTypeCategories(
            $this->_request->getParam('action')
        );
    }

    public function researchAction()
    {
        $this->view->user = new Application_Model_UserSettings();
        $techtree         = new Application_Model_TechTreeItems();
        $selectedCategory = $this->_request->getParam('category');
        if ($selectedCategory !== null && $selectedCategory != '') {
            $categoryName          = $techtree->getCategoryName(
                $selectedCategory
            );
            $this->view->typeItems = array(
                $categoryName => $techtree->getCategoryItems(
                    $selectedCategory
                )
            );
        } else {
            $this->view->typeItems = $techtree->getTypeItems(
                $this->_request->getParam('action')
            );
        }

        $this->view->typeCategories = $techtree->getTypeCategories(
            $this->_request->getParam('action')
        );
    }

    public function shipAction()
    {
        $this->view->user = new Application_Model_UserSettings();
        $techtree         = new Application_Model_TechTreeItems();
        $selectedCategory = $this->_request->getParam('category');
        if ($selectedCategory !== null && $selectedCategory != '') {
            $categoryName          = $techtree->getCategoryName(
                $selectedCategory
            );
            $this->view->typeItems = array(
                $categoryName => $techtree->getCategoryItems(
                    $selectedCategory
                )
            );
        } else {
            $this->view->typeItems = $techtree->getTypeItems(
                $this->_request->getParam('action')
            );
        }

        $this->view->typeCategories = $techtree->getTypeCategories(
            $this->_request->getParam('action')
        );
    }

    public function defenseAction()
    {
        $this->view->user = new Application_Model_UserSettings();
        $techtree         = new Application_Model_TechTreeItems();
        $selectedCategory = $this->_request->getParam('category');
        if ($selectedCategory !== null && $selectedCategory != '') {
            $categoryName          = $techtree->getCategoryName(
                $selectedCategory
            );
            $this->view->typeItems = array(
                $categoryName => $techtree->getCategoryItems(
                    $selectedCategory
                )
            );
        } else {
            $this->view->typeItems = $techtree->getTypeItems(
                $this->_request->getParam('action')
            );
        }

        $this->view->typeCategories = $techtree->getTypeCategories(
            $this->_request->getParam('action')
        );
    }

    public function searchAction()
    {
        $searchTerm = $this->_request->getParam('searchTerm');
        if ($searchTerm !== null) {
            $techTree      = new Application_Model_TechTreeItems();
            $searchResults = $techTree->search(
                $this->_request->getParam('searchTerm')
            );
            if (count($searchResults) == 1) {
                $resultKeys = array_keys($searchResults);
                $this->_response->setRedirect(
                    $this->view->url(
                        array(
                            'controller' => 'objects',
                            'action'     => 'details',
                            'id'         => $resultKeys[0],
                        ),
                        null,
                        true
                    )
                );
            } else {
                $this->view->searchTerm = $searchTerm;
            }
        }
    }

    public function detailsAction()
    {
        $itemName                  = $this->_request->getParam('id', 'metalmine');
        $techtree                  = new Application_Model_TechTreeItems();
        $this->view->item          = $techtree->getItem($itemName);
        $this->view->itemDepencies = $techtree->getItemDepencies($itemName);
        $this->view->user          = new Application_Model_UserSettings();
    }

    public function searchAjaxAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        $techTree                  = new Application_Model_TechTreeItems();
        $searchResults             = $techTree->search(
            $this->_request->getParam('searchTerm')
        );
        $this->view->searchResults = $searchResults;
    }

    public function reverseAction()
    {
        $techtree          = new Application_Model_TechTreeItems();
        $this->view->item  = $techtree->getItem(
            $this->_request->getParam('id', 'metalmine')
        );
        $this->view->items = $techtree->getReverseDepencies(
            $this->_request->getParam('id', 'metalmine')
        );
    }
}
