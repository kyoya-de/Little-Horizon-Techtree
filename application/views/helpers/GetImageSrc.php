<?php
/**
 *
 * @author Darky
 * @version 
 */
class Zend_View_Helper_GetImageSrc extends Zend_View_Helper_Abstract
{
    public function getImageSrc($imageName)
    {
        return $this->view->baseUrl() . '/images/' . $imageName;
    }
}

