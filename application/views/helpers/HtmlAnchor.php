<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Darky
 * Date: 28.05.11
 * Time: 12:45
 * To change this template use File | Settings | File Templates.
 */
 
class Zend_View_Helper_HtmlAnchor extends Zend_View_Helper_HtmlElement
{
    public function htmlAnchor($url, $text, $class = null, $target = null)
    {
        $attribs = array(
            'href' => $this->view->url($url, null, true),
        );
        if ($class !== null) {
            $attribs['class'] = (string) $class;
        }
        if ($target !== null) {
            $attribs['target'] = (string) $target;
        }
        $startTag = '<a' . $this->_htmlAttribs($attribs) . '>';
        $endTag = '</a>';
        return $startTag . $text . $endTag;
    }
}
