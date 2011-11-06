<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    }

    public function statisticsAction()
    {
        $techtree = new Application_Model_TechTreeItems();
        $this->view->stats = $techtree->getStatistics();
    }


}





