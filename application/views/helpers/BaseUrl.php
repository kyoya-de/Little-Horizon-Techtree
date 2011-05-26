<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseUrl
 *
 * @author Darky
 */
class Zend_View_Helper_BaseUrl extends Zend_View_Helper_Abstract
{
    public function baseUrl()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $fcBaseUrl = $frontController->getBaseUrl();
        $urlParts = explode('index.php', $fcBaseUrl);
        return $urlParts[0];
    }
}
