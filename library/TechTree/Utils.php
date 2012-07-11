<?php
final class TechTree_Utils
{
    /**
     * Disabled class constructor, because this is a static class.
     *
     * @return TechTree_Utils
     */
    private function __construct()
    {
    }

    /**
     * Converts a MySQL datetime string into an Unix-Timestamp.
     *
     * @param string $mysqlTs    MySQL datetime string.
     * @param string $dateFormat Output format, plain Unix-Timestamp if it's missing. @see date()
     *
     * @static
     *
     * @return int|string
     */
    public static function mysqlTs2UnixTs($mysqlTs, $dateFormat = null)
    {
        $reg = '/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/';
        preg_match($reg, $mysqlTs, $arg);
        $unixTs = mktime($arg[4], $arg[5], $arg[6], $arg[2], $arg[3], $arg[1]);
        if ($dateFormat === null) {
            return $unixTs;
        }
        return date($dateFormat, $unixTs);
    }

    /**
     * Returns an array with all available page styles/themes.
     *
     * @static
     *
     * @return array
     */
    public static function getAvailableStyles()
    {
        $path   = realpath(APPLICATION_PATH . '/../public/css');
        $files  = glob($path . DIRECTORY_SEPARATOR . '*.css');
        $result = array();
        foreach ($files as $file) {
            $filePointer = fopen($file, 'r');
            $style       = fgets($filePointer);
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

    /**
     * Returns an array with all available account types.
     *
     * @static
     *
     * @return array
     */
    public static function getAccountTypes()
    {
        return array(
            'admin' => 'Administrator',
            'user'  => 'Benutzer',
        );
    }

    /**
     * Returns an array with all available item types.
     *
     * @static
     *
     * @return array
     */
    public static function getItemTypes()
    {
        return array(
            'root'     => 'Root',
            'type'     => 'Typ',
            'category' => 'Kategorie',
            'item'     => 'Objekt',
        );
    }

    /**
     * Returns an array with all available In-Game races.
     *
     * @static
     *
     * @return array
     */
    public static function getItemRaces()
    {
        return array(
            'normal'   => 'Normal',
            'diggren'  => 'Diggren',
            'keelaak'  => 'Kee\'laak',
            'nux'      => 'Nux',
            'quipgrex' => 'Quipgrex',
            'sciween'  => 'Sciween',
        );
    }
}
