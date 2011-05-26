<?php
/**
 *
 * @author Darky
 * @version 
 */
class Zend_View_Helper_Bbcode extends Zend_View_Helper_Abstract
{
    public function detailsCallback($match)
    {
        $techtree = new Application_Model_TechTreeItems();
        $itemInfo = $techtree->getItem($match[1]);
        return $this->view->hyperlink(
            $itemInfo['dname'],
            array(
                'controller' => 'objects',
                'action' => 'details',
                'id' => $itemInfo['name'],
            )
        ) . ' (' . $itemInfo['name'] . ')';
    }
    
    public function bbcode($text)
    {
        $text = stripslashes($text);
        $text = htmlspecialchars($text);
        $text = preg_replace('/\\[b\\](.*)\\[\\/b\\]/isU', '<b>$1</b>', $text);
        $text = preg_replace('/\\[i\\](.*)\\[\\/i\\]/isU', '<i>$1</i>', $text);
        $text = preg_replace('/\\[u\\](.*)\\[\\/u\\]/isU', '<u>$1</u>', $text);
        $text = preg_replace(
            '/\\[color=([^\\]]+)\\](.*)\\[\\/color\\]/isU',
            '<span style="color:$1;">$2</span>',
            $text
        );
        $text = preg_replace(
            '/\\[url=([^\\]]+)\\](.*)\\[\\/url\\]/isU',
            '<a href="$1" target="_blank">$2</a>',
            $text
        );
        $text = preg_replace(
            '/\\[url\\](.*)\\[\\/url\\]/isU',
            '<a href="$1" target="_blank">$1</a>',
            $text
        );
        $text = preg_replace_callback(
            '/\\[details=(\\w+)\\]/isU',
            array($this, 'detailsCallback'),
            $text
        );
        
        $smilies = TechTree_Bbcode_Smilies::getSmileyList();
        
        foreach ($smilies as $smiley => $image) {
            $text = str_replace(
                $smiley,
                '<img src="' . $this->view->getImageSrc($image) . '" alt="" />',
                $text
            );
        }
        return nl2br($text);
    }
}

