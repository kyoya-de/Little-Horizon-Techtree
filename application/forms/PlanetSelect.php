<?php

class Application_Form_PlanetSelect extends Zend_Form
{
    private static $_planets = array();

    public function init()
    {
        $onChange = 'document.getElementById(\'planet_select\').submit();';
        $this->setAttrib('id', 'planet_select');
        $this->addElement(
            'select',
            'planet',
            array(
                'label'                        => '',
                'onchange'                     => $onChange,
                'multiOptions'                 => self::$_planets,
                'decorators'                   => array('ViewHelper'),
                'disableLoadDefaultDecorators' => true,
            )
        );
        $frontController = Zend_Controller_Front::getInstance();
        $currentParams   = $frontController->getRequest()->getParams();
        $actionParams    = array(
            'controller' => 'user',
            'action'     => 'set-planet',
        );
        foreach ($currentParams as $key => $value) {
            $actionParams['last-' . $key] = $value;
        }
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setAction($view->url($actionParams, null, true));
    }

    public static function setPlanets(array $planets)
    {
        self::$_planets = $planets;
    }
}

