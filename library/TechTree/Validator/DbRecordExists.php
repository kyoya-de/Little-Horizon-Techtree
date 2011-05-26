<?php
class TechTree_Validator_DbRecordExists extends TechTree_Validator_Db
    implements Zend_Validate_Interface
{
    public function isValid($value)
    {
        $this->_isValid = false;
        $checkSql = 'SELECT `' . $this->_columnName . '` FROM `' .
            $this->_tableName . '` WHERE `' . $this->_columnName . '` = :VALUE';
        
        $preparedStatement = $this->_dbObject->prepare($checkSql);
        
        $pdoResult = $preparedStatement->execute(array('VALUE' => $value));
        
        if ($pdoResult != true) {
            return false;
        }
        
        $resultRow = $preparedStatement->fetch(PDO::FETCH_ASSOC);
        
        if ($resultRow === false) {
            return false;
        }
        
        $preparedStatement->closeCursor();
        
        $this->_isValid = true;
        return true;
    }
    
    public function getMessages()
    {
        if ($this->_isValid === null) {
            throw new TechTree_Validator_Exception(
                'You must call \'isValid()\' first.'
            );
        }
        
        if ($this->_isValid) {
            return array();
        }
        
        if (!$this->_isValid) {
            return array(
                'DB_RECORD_EXISTS' => 'A record with this value doesn\'t ' .
                    'exists in DB.'
            );
        }
    }
}
