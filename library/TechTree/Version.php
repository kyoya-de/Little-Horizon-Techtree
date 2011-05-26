<?php
/**
 * Class to store and retrieve the version of TechTree.
 *
 * @author Darky
 */
class TechTree_Version
{
    const VERSION = '4.0.1';
    
    const CODENAME = 'Heart';

    /**
     * Compare the specified TechTree version string $version
     * with the current TechTree_Version::VERSION of TechTree.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return boolean           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }
}
