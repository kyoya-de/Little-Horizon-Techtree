<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->addElementPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $this->addElement(
            'text',
            'username',
            array(
                'class' => 'input-username',
                'label' => 'Nick',
                'required' => true,
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'password',
            'password',
            array(
                'class' => 'input-password',
                'label' => 'Pass',
                'required' => true,
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'checkbox',
            'stayLoggedIn',
            array(
                'class' => 'input-checkbox',
                'label' => 'Auto Login',
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'submit',
            'login',
            array(
                'class' => 'submit-login',
                'label' => '',
                'value' => 'Login',
                'decorators' => array('Login'),
            )
        );
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setAction(
            $view->url(
                array(
                    'controller' => 'user',
                    'action' => 'login',
                ),
                null,
                true
            )
        );
    }
}

