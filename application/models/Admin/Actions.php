<?php
class Application_Model_Admin_Actions
{
    public function __construct()
    {
    }
    
    public function categoryEdit(array $params)
    {
        $admin = new Application_Model_Admin_Objects();
        $admin->categoryEdit($params);
    }
    
    public function categoryDelete(array $params)
    {
        $admin = new Application_Model_Admin_Objects();
        $admin->categoryDelete($params);
    }
    
    public function objectEdit(array $params)
    {
        $admin = new Application_Model_Admin_Objects();
        $admin->objectEdit($params);
    }
    
    public function objectDelete(array $params)
    {
        $admin = new Application_Model_Admin_Objects();
        $admin->objectDelete($params);
    }
    
    public function userEdit(array $params)
    {
        $admin = new Application_Model_Admin_Users();
        $admin->userEdit($params);
    }
    
    public function userDelete(array $params)
    {
        $admin = new Application_Model_Admin_Users();
        $admin->userDelete($params);
    }
    
    public function userActiveToggle(array $params)
    {
        $admin = new Application_Model_Admin_Users();
        $admin->userActiveToggle($params);
    }
}
