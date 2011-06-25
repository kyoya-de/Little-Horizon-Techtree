<?php
/**
 * This file is a part of the Little Horizon Community TechTree project.
 * The whole project is licensed under the terms of the MIT license.
 * Take a look at the LICENSE file for information your rights.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage Library
 * @version    4.1.2
 * @author     Stefan Krenz
 */

/**
 * Class to store version information.
 *
 * @package    Little-Horizon-TechTree
 * @subpackage Library
 */
class TechTree_Version
{
    /**
     * Current project version.
     */
    const VERSION = '4.1.2';

    /**
     * Current codename of the project.
     * It may change an every minor version update.
     * If the major version changes, the source of the name pool will be also changed.
     *
     * Name pool in version 4: human organs
     * Name pool in version 5: eras
     */
    const CODENAME = 'Tongue';
}
