<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetStyle
 *
 * @author Darky
 */
class Zend_View_Helper_GetStyle extends Zend_View_Helper_Abstract
{
    public function getStyle()
    {
        return TechTree_Session::getNamespace('style')->style;
    }
}
