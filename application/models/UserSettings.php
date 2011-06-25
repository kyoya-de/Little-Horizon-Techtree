<?php

class Application_Model_UserSettings extends TechTree_Db_Model
{
    public function getSettings($userId)
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        $userSql = 'SELECT `style`, `techs_id`, `account_type` ' .'
            FROM `tt_users` WHERE `id` = :USERID';
        $pdoStatement = $this->_dbObject->prepare($userSql);
        $pdoStatement->execute(array('USERID' => $userId,));
        $basicSettings = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $pdoStatement->closeCursor();
        
        $userLevelPlanet = $this->getPlanetTechs(
            $authSession->techsId,
            $authSession->currentPlanet
        );
        ksort($userLevelPlanet);
        
        $userLevelResearch = $this->getPlanetTechs(
            $authSession->techsId,
            'Forschungen'
        );
        ksort($userLevelResearch);
        
        return array(
            'basicSettings' => $basicSettings,
            'userLevelPlanet' => $userLevelPlanet,
            'userLevelResearch' => $userLevelResearch,
        );
    }

    public function getPlanetTechs($techId, $planet)
    {
        if (($techId === null) || ($planet === null)) {
            return array();
        }
        $planetTechsSql = 'SELECT l.`techid`, l.`level`, u.`dname` FROM ' .
            '`tt_userlevel` l LEFT JOIN `tt_units` u ON ' .
            'u.`name` = l.`techid` WHERE l.`userid` = :USERID AND ' .
            'l.`planet` = :PLANET';
        $pdoStatement = $this->_dbObject->prepare($planetTechsSql);
        $data = array(
            'USERID' => $techId,
            'PLANET' => $planet,
        );
        $pdoResult = $pdoStatement->execute($data);
        if ($pdoResult === false) {
        }
        $result = array();
        while (false !== ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC))) {
            $result[$row['techid']] = array(
                'level' => $row['level'],
                'dname' => $row['dname'],
            );
        }
        return $result;
    }

    public function getRegisteredPlanets($techId = null)
    {
        if ($techId == null) {
            $authSession = TechTree_Session::getNamespace('Auth');
            $techId = $authSession->techsId;
        }
        $planetsSql = 'SELECT `planet` FROM `tt_userlevel` ' .
            'WHERE `userid` = :USERID AND NOT `planet` =\'Forschungen\' ' .
            'GROUP BY `planet`';
        $pdoStatement = $this->_dbObject->prepare($planetsSql);
        $pdoStatement->execute(array('USERID' => $techId));
        $result = array();
        while (false !== ($row = $pdoStatement->fetch(PDO::FETCH_ASSOC))) {
            $result[$row['planet']] = $row['planet'];
        }
        return $result;
    }
    
    public function setBasicSettings($userId, array $settings)
    {
        $saveSql = 'UPDATE `tt_users` SET `active` = 1';
        foreach (array_keys($settings) as $settingKey) {
            $saveSql .= ', `' . $settingKey . '` = :' . $settingKey;
        }
        $saveSql .= ' WHERE `id` = :USERID';
        $settings += array('USERID' => $userId);
        $pdoStatement = $this->_dbObject->prepare($saveSql);
        $pdoStatement->execute($settings);
        
        TechTree_Session::getNamespace('Auth')->style = $settings['style'];
        TechTree_Session::getNamespace('Auth')->techsId = $settings['techs_id'];
    }
    public function setPassword($password)
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        $passwordSql = 'UPDATE `tt_users` SET `password` = MD5(' .
            $this->_dbObject->quote($password) . ') WHERE `id` = ' .
            $authSession->id;
        $this->_dbObject->query($passwordSql);
        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        if ($request->get('tt_autologin') !== null) {
            $crypt = TechTree_Crypt::getInstance('lh_techtree');
            $identity = $crypt->encrypt(
                $authSession->username . ':' . $password
            );
            $frontController = Zend_Controller_Front::getInstance();
            $request = $frontController->getRequest();
            setcookie(
                'tt_autologin',
                $identity . ':' . md5($identity),
                time() + 365 * 24 * 60 * 60,
                $frontController->getBaseUrl() . '/',
                $request->get('SERVER_NAME')
            );
        }
    }
    public function deletePlanet($planet)
    {
        $deleteSql = 'DELETE FROM `tt_userlevel` WHERE `planet` = ' .
            $this->_dbObject->quote($planet);
        $this->_dbObject->query($deleteSql);
    }
    
    public function createPlanet($planet)
    {
        $createSql = 'INSERT INTO `tt_userlevel` ' .
            '(`userid`, `planet`, `techid`, `level`) VALUES ' .
            '(:techId, :planet, \'metalmine\', 0);';
        $pdoState = $this->_dbObject->prepare($createSql);
        $pdoState->execute(
            array(
                'techId' => TechTree_Session::getNamespace('Auth')->techsId,
                'planet' => $planet,
            )
        );
    }

    public function setNewPlanet($userId, $newPlanet)
    {
        $saveSql = 'UPDATE `tt_users` SET `current_planet` = :PLANET ' .
            'WHERE `id` = :USERID';
        $pdoStatement = $this->_dbObject->prepare($saveSql);
        $pdoStatement->execute(
            array(
                'USERID' => $userId,
                'PLANET' => $newPlanet,
            )
        );
    }
    
    public function getUserTechLevel($objectId, $planet = null)
    {
        $authSession = TechTree_Session::getNamespace('Auth');
        if (!$authSession->loggedIn) {
            return 0;
        }
        if ($planet === null) {
            $planet = $authSession->currentPlanet;
        }
        $techSql = 'SELECT `level` FROM `tt_userlevel` WHERE ' .
            '`userid` = :userId AND `planet` = :planet AND `techid` = :techId';
        $pdoState = $this->_dbObject->prepare($techSql);
        $data = array(
            'userId' => $authSession->techsId,
            'planet' => $planet,
            'techId' => $objectId,
        );
        $pdoResult = $pdoState->execute($data);
        if ($pdoResult === false || $pdoState->rowCount() == 0) {
            return 0;
        }
        $row = $pdoState->fetch(PDO::FETCH_ASSOC);
        $pdoState->closeCursor();
        return $row['level'];
    }
    public function parseUserTechs($source, $techsId = null, $planet = null)
    {
        if ($techsId === null) {
            $authSession = TechTree_Session::getNamespace('Auth');
            $techsId = $authSession->techsId;
        }
        $replacements = array(
            "\r\n" => "\n",
            "\n\n" => "\n",
            "\t" => " ",
            "  " => " ",
        );
        $source = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $source
        );
        $sourceLines = explode("\n", $source);
        $objects = array();
        $deleteSql = 'DELETE FROM `tt_userlevel` WHERE `userid` = ' .
            $this->_dbObject->quote($techsId) . ' AND ' .
            '`planet` = :planet AND `techid` = :objectId';
        $insertSql = 'INSERT INTO `tt_userlevel` (`userid`, `planet`, ' .
            '`techid`, `level`) VALUES (' .
            $this->_dbObject->quote($techsId) . ', :planet, ' .
            ':objectId, :level)';
        $delState = $this->_dbObject->prepare($deleteSql);
        $insState = $this->_dbObject->prepare($insertSql);

        foreach ($sourceLines as $sourceLine) {
            $openBracket = strpos($sourceLine, '(');
            if ($openBracket === false) {
                continue;
            }

            $openBracket++;
            $firstSpace = strpos($sourceLine, ' ', $openBracket);
            $objectId = substr(
                $sourceLine,
                $openBracket,
                ($firstSpace - $openBracket)
            );
            $firstSpace++;
            $nextSpace = strpos($sourceLine, ' ', $firstSpace);
            $nextSpace++;
            $nextSpace = strpos($sourceLine, ' ', $nextSpace);
            $nextSpace++;
            $closeBracket = strpos($sourceLine, ')');
            $objectLevel = substr(
                $sourceLine,
                $nextSpace,
                ($closeBracket - $nextSpace)
            );
            $objects[$objectId] = ($objectLevel != '') ? $objectLevel : 0;
            $techtree = new Application_Model_TechTreeItems();
            $objectPath = $techtree->getItemPath($objectId);
            if ($planet === null) {
                $planet = $authSession->currentPlanet;
            }
            if ($objectPath['type']['name'] == 'research') {
                $planet = 'Forschungen';
            }
            $delState->execute(
                array(
                    'planet' => $planet,
                    'objectId' => $objectId,
                )
            );
            $insState->execute(
                array(
                    'planet' => $planet,
                    'objectId' => $objectId,
                    'level' => $objectLevel,
                )
            );
        }
        return $objects;
    }

    public function deleteUserTechs($userTechsId, $userPlanet, $userTechs)
    {
        $userTechs = implode("', '", $userTechs);
        $sql = "DELETE FROM `tt_userlevel` WHERE
            `userId` = '$userTechsId' AND
            `planet` = '$userPlanet' AND
            `techid` IN ('$userTechs')";
        $this->_dbObject->exec($sql);
    }
}

