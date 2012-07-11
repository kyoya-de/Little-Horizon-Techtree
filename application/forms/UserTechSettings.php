<?php

class Application_Form_UserTechSettings extends Zend_Form
{

    public function init()
    {
        $this->addPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $this->addElement(
            'textarea',
            'user_techs',
            array(
                'cols'       => '53',
                'rows'       => '15',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'submit',
            'save',
            array(
                'label'      => '',
                'value'      => 'Importieren',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'hidden',
            'settings_type',
            array(
                'label'      => '',
                'value'      => 'usertechs',
                'decorators' => array('Login'),
            )
        );
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setAction(
            $view->url(
                array(
                    'controller' => 'user',
                    'action'     => 'settings',
                ),
                null,
                true
            )
        );
    }
}

