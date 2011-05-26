<?php
class TechTree_Auth_Adapter_PDO implements Zend_Auth_Adapter_Interface
{
    /**
     * Database connection.
     * 
     * @var PDO
     */
    private $_dbObject = null;
    
    private $_tableName = null;
    
    private $_identityField = null;
    
    private $_credentialField = null;
    
    private $_identity = null;
    
    private $_credential = null;
    
    private $_passwordFunction = null;
    
    private $_customCondition = null;
    
    private $_resultRow = null;
    
    public function __construct(PDO $dbObject, $options)
    {
        $this->_dbObject = $dbObject;
        $this->_tableName = $options['tableName'];
        $this->_identityField = $options['identityField'];
        $this->_credentialField = $options['credentialField'];
        
        if (isset($options['passwordFunction'])) {
            $this->_passwordFunction = $options['passwordFunction'];
        }
        
        if (isset($options['customConditon'])) {
            $this->_customCondition = $options['customConditon'];
        }
    }
    
    public function setIdentity($username, $password)
    {
        $this->_identity = $username;
        $this->_credential = $password;
    }
    
    public function authenticate()
    {
        $preparedAuth = $this->_prepareAuth();
        
        $preparedAuth->bindParam('USER', $this->_identity);
        $preparedAuth->bindParam('PASS', $this->_credential);
        
        $dbResult = $preparedAuth->execute();
        
        $resultRow = $preparedAuth->fetch(PDO::FETCH_ASSOC);
        $preparedAuth->closeCursor();
        
        if ($dbResult == true && $resultRow !== false) {
            $this->_resultRow = $resultRow;
            unset($resultRow[$this->_credentialField]);
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $resultRow);
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, array());
        }
    }
    
    public function getResultRow()
    {
        return $this->_resultRow;
    }
    
    private function _prepareAuth()
    {
        $userCheckSql = 'SELECT * FROM `' . $this->_tableName . '` WHERE ' .
            '`' . $this->_identityField . '` = :USER AND `' .
            $this->_credentialField . '` = ';
        if ($this->_passwordFunction !== null) {
            $userCheckSql .= str_replace(
                '?',
                ':PASS',
                $this->_passwordFunction
            );
        } else {
            $userCheckSql .= ':PASS';
        }
        
        if ($this->_customCondition !== null) {
            $userCheckSql .= ' AND ' . $this->_customCondition;
        }
        return $this->_dbObject->prepare($userCheckSql);
    }
    
    public function getIdentity()
    {
        return array(
            'identity' => $this->_identity,
            'credential' => $this->_credential,
        );
    }
}
