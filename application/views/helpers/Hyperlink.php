<?php
/**
 *
 * @author Darky
 * @version 
 */
class Zend_View_Helper_Hyperlink extends Zend_View_Helper_Abstract
{
    public function hyperlink($title, $url, $params = null)
    {
        if (is_array($url)) {
            $url = $this->view->url($url, null, true);
        }
        $link = '<a href="' . $url . '"';
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $link .= ' ' . $key . '="' . $value . '"';
            }
        }
        $link .= '>' . $this->view->escape($title) . '</a>';
        return $link;
    }
}
