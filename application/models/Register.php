<?php

class Application_Model_Register extends TechTree_Db_Model
{
    public function registerUser(array $params)
    {
        $insertQuery = 'INSERT INTO `tt_users` ' .
            '(`username`, `password`, `style`, `techs_id`) VALUES ' .
            '(:username, MD5(:password), :style, UUID())';
        $data = array(
            'username' => $params['username'],
            'password' =>$params['password'],
            'style' => $params['default_style'],
        );
        $pdoStatement = $this->_dbObject->prepare($insertQuery);
        $result = $pdoStatement->execute($data);
        if (!$result) {
            var_dump($pdoStatement->errorInfo());
        }
        return $result;
    }

}

