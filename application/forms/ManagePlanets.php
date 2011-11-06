<?php
class Application_Form_ManagePlanets extends Zend_Form
{
    public function init()
    {
        $this->addPrefixPath(
            'TechTree_Decorator',
            'TechTree/Decorator/',
            'decorator'
        );
        $userSettings = new Application_Model_UserSettings();
        $authSession = TechTree_Session::getNamespace('Auth');
        $planets = $userSettings->getRegisteredPlanets($authSession->techsId);
        $this->addElement(
            'select',
            'planet',
            array(
                'label' => 'Planet',
                'multiOptions' => $planets,
                'decorators' => array('Login'),
                'DisableLoadDefaultDecorators' => true,
            )
        );
        $this->addElement(
            'checkbox',
            'deletePlanet',
            array(
                'label' => 'Gewählten Planet löschen',
                'checkedvalue' => '1',
                'decorators' => array('Login'),
                'DisableLoadDefaultDecorators' => true,
            )
        );
        $this->addElement(
            'text',
            'newPlanet',
            array(
                'label' => 'Neuer Planet',
                'decorators' => array('Login'),
                'DisableLoadDefaultDecorators' => true,
            )
        );
        $this->addElement(
            'hidden',
            'settings_type',
            array(
                'label' => '',
                'value' => 'managePlanet',
                'decorators' => array('Login'),
                'DisableLoadDefaultDecorators' => true,
            )
        );
        $this->addElement(
            'submit',
            'manage',
            array(
                'label' => '',
                'value' => 'Speichern',
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
