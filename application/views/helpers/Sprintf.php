<?php
require_once ('Zend\View\Helper\Abstract.php');
class Zend_View_Helper_Sprintf extends Zend_View_Helper_Abstract
{
    public function sprintf()
    {
        $result = call_user_func_array('sprintf', func_get_args());
        return str_replace(' ', '&nbsp;', $result);
    }
}
