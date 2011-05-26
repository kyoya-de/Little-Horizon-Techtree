<?php
final class TechTree_Utils
{
    private function __construct()
    {
    }
    
    public static function mysqlTs2UnixTs($mysqlTs, $dateFormat = null)
    {
        $reg = '/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/';
        preg_match($reg, $mysqlTs, $arg);
        $unixTs =  mktime($arg[4], $arg[5], $arg[6], $arg[2], $arg[3], $arg[1]);
        if ($dateFormat === null) {
            return $unixTs;
        }
        return date($dateFormat, $unixTs);
    }
    
    public static function getAvailableStyles()
    {
        $path = realpath(APPLICATION_PATH . '/../public/css');
        $files = glob($path . DIRECTORY_SEPARATOR . '*.css');
        $result = array();
        foreach ($files as $file) {
            $filePointer = fopen($file, 'r');
            $style = fgets($filePointer);
            fclose($filePointer);
            $styleId = str_replace('.css', '', basename($file));
            if (preg_match('/\$Name: ([^\$]+)\$/', $style, $args)) {
                $result[$styleId] = $args[1];
            } else {
                $result[$styleId] = $styleId;
            }
        }
        return $result;
    }
    public static function getAccountTypes()
    {
        return array(
            'admin' => 'Administrator',
            'user' => 'Benutzer',
        );
    }
    public static function getItemTypes()
    {
        return array(
            'root' => 'Root',
            'type' => 'Typ',
            'category' => 'Kategorie',
            'item' => 'Objekt',
        );
    }
    
    public static function getItemRaces()
    {
        return array(
            'normal' => 'Normal',
            'diggren' => 'Diggren',
            'keelaak' => 'Kee\'laak',
            'nux' => 'Nux',
            'quipgrex' => 'Quipgrex',
            'sciween' => 'Sciween',
        );
    }
}
