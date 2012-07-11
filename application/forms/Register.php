<?php

class Application_Form_Register extends Zend_Form
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
            'text',
            'username',
            array(
                'label'      => 'Nickname:',
                'class'      => 'input-username',
                'required'   => true,
                'decorators' => array('Login'),
                'validators' => array(
                    new TechTree_Validator_DbRecordNotExists(
                        TechTree_Db_Model::getDefaultDbConnection(),
                        'tt_users',
                        'username'
                    ),
                ),
            )
        );
        $this->addElement(
            'password',
            'password',
            array(
                'label'      => 'Passwort',
                'class'      => 'input-password',
                'required'   => true,
                'decorators' => array('Login'),
                'validators' => array('Password'),
            )
        );
        $this->addElement(
            'password',
            'password_confirm',
            array(
                'label'      => '... bestätigen',
                'class'      => 'input-password',
                'required'   => true,
                'decorators' => array('Login'),
            )
        );
        $this->addElement(
            'select',
            'default_style',
            array(
                'label'        => 'Standard-Layout',
                'class'        => 'select-style',
                'decorators'   => array('Login'),
                'multiOptions' => array(
                    'blue'  => 'Blau',
                    'green' => 'Grün',
                    'red'   => 'Rot',
                )
            )
        );
        $this->addElement(
            'submit',
            'submit',
            array(
                'label'      => '',
                'value'      => 'Registrieren',
                'decorators' => array('Login'),
            )
        );
        $view = Zend_Layout::getMvcInstance()->getView();
        $this->setAction(
            $view->url(
                array(
                    'controller' => 'user',
                    'action'     => 'register',
                ),
                null,
                true
            )
        );
    }
}

