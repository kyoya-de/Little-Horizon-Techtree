<?php

class Application_Form_UserBasicSettings extends Zend_Form
{

    public function init()
    {
        $this->addPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $this->addElement(
            'select',
            'style',
            array(
                'label' => 'Style',
                'class' => 'select-style',
                'decorators' => array('Login'),
                'multiOptions' => TechTree_Utils::getAvailableStyles(),
            )
        );

        $this->addElement(
            'text',
            'techs_id',
            array(
                'label' => 'UserTechs ID',
                'class' => 'input-techs-id',
                'required' => true,
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'submit',
            'save',
            array(
                'label' => '',
                'value' => 'Speichern',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'hidden',
            'user_id',
            array(
                'label' => '',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'hidden',
            'settings_type',
            array(
                'label' => '',
                'value' => 'basic',
                'decorators' => array('Login'),
            )
        );
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setAction(
            $view->url(
                array(
                    'controller' => 'user',
                    'action' => 'settings',
                ),
                null,
                true
            )
        );
    }


}

