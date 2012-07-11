<?php
class TechTree_Auth_Adapter_PDO implements Zend_Auth_Adapter_Interface
{
    /**
     * Database connection.
     *
     * @var PDO
     */
    private $_dbObject = null;

    /**
     * @var string
     */
    private $_tableName = null;

    /**
     * @var string
     */
    private $_identityField = null;

    /**
     * @var string
     */
    private $_credentialField = null;

    /**
     * @var string
     */
    private $_identity = null;

    /**
     * @var string
     */
    private $_credential = null;

    /**
     * @var string
     */
    private $_passwordFunction = null;

    /**
     * @var string
     */
    private $_customCondition = null;

    /**
     * @var array
     */
    private $_resultRow = null;

    /**
     * Initializes the authentication adapter.
     *
     * @param PDO   $dbObject Instance of a connected PDO object.
     * @param array $options  Options for the authentication process.
     *
     * @return TechTree_Auth_Adapter_PDO
     */
    public function __construct(PDO $dbObject, $options)
    {
        $this->_dbObject        = $dbObject;
        $this->_tableName       = $options['tableName'];
        $this->_identityField   = $options['identityField'];
        $this->_credentialField = $options['credentialField'];

        if (isset($options['passwordFunction'])) {
            $this->_passwordFunction = $options['passwordFunction'];
        }

        if (isset($options['customConditon'])) {
            $this->_customCondition = $options['customConditon'];
        }
    }

    /**
     * Sets the identity and credentials for the authentication process.
     *
     * @param string $username Name (identity) of the user.
     * @param string $password Password (credentials) for the user account.
     *
     * @return void
     */
    public function setIdentity($username, $password)
    {
        $this->_identity   = $username;
        $this->_credential = $password;
    }

    /**
     * Authenticates a user with the given identity (username) and the credentials (password).
     *
     * @return Zend_Auth_Result
     */
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

    /**
     * Returns the database table row of the logged-in user.
     *
     * @return array|null
     */
    public function getResultRow()
    {
        return $this->_resultRow;
    }

    /**
     * Prepares the login SQL query.
     *
     * @return PDOStatement
     */
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

    /**
     * Returns the identity of the logged-in user.
     *
     * @return array
     */
    public function getIdentity()
    {
        return array(
            'identity'   => $this->_identity,
            'credential' => $this->_credential,
        );
    }
}
