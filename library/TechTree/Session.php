<?php
/**
 * 
 *
 * @author Darky
 */
class TechTree_Session
{
    private static $_namespaces = array();

    const SESSION_NAMESPACE = 'TechTree_Session_';

    public static function getNamespace($namespace)
    {
        if (!isset(self::$_namespaces[$namespace])) {
            self::$_namespaces[$namespace] = new Zend_Session_Namespace(
                self::SESSION_NAMESPACE . $namespace
            );
        }

        return self::$_namespaces[$namespace];
    }

    public static function clear($namespace)
    {
        $session = self::getNamespace($namespace);
        $session->unsetAll();
    }

    public static function clearAll($onlyOwnNamespaces = true)
    {
        $dummy = self::getNamespace('dummy');
        $namespaces = array_keys($_SESSION);
        foreach ($namespaces as $namespace) {
            $sessionNSName = substr(
                $namespace,
                0,
                strlen(self::SESSION_NAMESPACE)
            );
            $isOwnNS = $sessionNSName == self::SESSION_NAMESPACE;
            if ($isOwnNS || !$onlyOwnNamespaces) {
                unset($_SESSION[$namespace]);
            }
        }
        unset($dummy);
    }
}
