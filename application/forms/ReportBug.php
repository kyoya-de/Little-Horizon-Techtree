<?php

class Application_Form_ReportBug extends Zend_Form
{

    public function init()
    {
        $this->addPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $this->addElement(
            'text',
            'summary',
            array(
                'label' => 'Zusammenfassung',
                'required' => true,
                'decorators' => array('Login'),
                'size' => 50,
            )
        );
        $this->addElement(
            'textarea',
            'details',
            array(
                'label' => 'Beschreibung',
                'required' => true,
                'decorators' => array('Login'),
                'rows' => 10,
                'cols' => 40,
            )
        );
        $this->addElement(
            'submit',
            'report',
            array(
                'label' => '',
                'value' => 'Melden',
                'decorators' => array('Login'),
            )
        );
    }
}
