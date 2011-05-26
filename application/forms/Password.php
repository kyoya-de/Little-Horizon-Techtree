<?php

class Application_Form_Password extends Zend_Form
{

    public function init()
    {
        $this->addElementPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $this->addElementPrefixPath(
            'TechTree_Validator',
            'TechTree/Validator/',
            'validate'
        );

        $this->addElement(
            'password',
            'password',
            array(
                'label' => 'Passwort',
                'class' => 'input-password',
                'required' => true,
                'decorators' => array('Login'),
                'validators' => array('Password'),
            )
        );
        $this->addElement(
            'password',
            'password_confirm',
            array(
                'label' => '... bestätigen',
                'class' => 'input-password',
                'required' => true,
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'submit',
            'submit',
            array(
                'label' => '',
                'value' => 'Passwort ändern',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'hidden',
            'settings_type',
            array(
                'label' => '',
                'value' => 'setPassword',
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

