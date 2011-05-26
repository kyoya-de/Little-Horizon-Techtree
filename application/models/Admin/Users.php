<?php
class Application_Model_Admin_Users extends TechTree_Db_Model
{
    public function getUserInfo($userId)
    {
        $userSql = 'SELECT `id`, `username`, ' .
            'IF(LENGTH(`password`) > 0, 1, 0) passSet, `style`, ' .
            '`account_type` accountType, `techs_id` techsId, ' .
            '`current_planet` currentPlanet, `active` FROM `tt_users` '.
            'WHERE `id` = ' . $this->_dbObject->quote($userId);
        $pdoState = $this->_dbObject->query($userSql);
        $user = array();
        if ($pdoState === false) {
            return $user;
        }
        
        $user = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        return $user;
    }
    
    public function userEdit(array $params)
    {
        $userId = $params['userId'];
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($userId == 1 && $authSession->id != $userId) {
            return 0;
        }
        unset(
            $params['action'],
            $params['controller'],
            $params['module'],
            $params['doAction']
        );
        
        $userUpdateSql = 'UPDATE `tt_users` SET `username` = :username, ' .
            '`account_type` = :accountType, ';
        if (isset($params['password']) && strlen($params['password']) > 0) {
            $userUpdateSql .= '`password` = MD5(:password), ';
        }
        
        $userUpdateSql .= '`style` = :style, `techs_id` = :techsId, ' .
            '`active` = :active, `current_planet` = :currentPlanet ' .
            'WHERE `id` = :userId';
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($params['userId'] == $authSession->id) {
            $authSession->style = $params['style'];
        }
        $pdoState = $this->_dbObject->prepare($userUpdateSql);
        $pdoResult = $pdoState->execute($params);
        return $pdoResult;
    }
    
    public function userDelete(array $params)
    {
        $userId = $params['userId'];
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($userId == 1 && $authSession->id != $userId) {
            return 0;
        }
        $userDeleteSql = 'DELETE FROM `tt_users` WHERE `id` = ' .
            $this->_dbObject->quote($userId);
        $pdoState = $this->_dbObject->query($userDeleteSql);
        if ($pdoState === false) {
            return 0;
        } else {
            return 1;
        }
    }
    
    public function userActiveToggle(array $params)
    {
        $userId = $params['id'];
        $authSession = TechTree_Session::getNamespace('Auth');
        if ($userId == 1 && $authSession->id != $userId) {
            return 0;
        }
        $userToggleSql = 'UPDATE `tt_users` SET `active` = IF(`active`=1,0,1)' .
            'WHERE `id` = :userId';
        $pdoState = $this->_dbObject->prepare($userToggleSql);
        $pdoResult = $pdoState->execute(array('userId' => $userId));
        return $pdoResult;
    }
    
    public function getUsers()
    {
        $usersSql = 'SELECT `id`, `username`, ' .
            'IF(LENGTH(`password`) > 0, 1, 0) passSet, ' .
            '`account_type` accountType, `active` FROM `tt_users`';
        
        $pdoState = $this->_dbObject->query($usersSql);
        
        $users = array();
        
        if ($pdoState === false) {
            return $users;
        }
        
        while (false !== ($row = $pdoState->fetch(PDO::FETCH_ASSOC))) {
            $userId = $row['id'];
            unset($row['id']);
            $users[$userId] = $row;
        }
        
        return $users;
    }
}
