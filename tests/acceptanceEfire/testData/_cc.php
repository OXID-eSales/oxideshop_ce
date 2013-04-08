<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: _cc.php 48931 2012-08-22 13:57:51Z vilma $
 */

/**
 * This script unsets all domain cookies and cache
 */
require 'bootstrap.php';
/**
 * Delete all files and dirs recursively
 *
 * @param string $dir directory to delete
 *
 * @return null
 */
function rrmdir($dir)
{
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) {
            rrmdir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dir);
}
if ( $sCompileDir = oxRegistry::get('oxConfigFile')->getVar('sCompileDir') ) {
    foreach (glob($sCompileDir."/*") as $file) {
        if (is_dir($file)) {
            rrmdir($file);
        } else {
            unlink($file);
        }
    }
}


// Clean tmp
if (isset($_SERVER['HTTP_COOKIE'])) {
    $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($aCookies as $sCookie) {
        $sRawCookie = explode('=', $sCookie);
        setcookie(trim( $sRawCookie[0] ), '', time() - 10000, '/');
    }
}

if ( !isset( $_GET['no_redirect'])) {
    header("Location: ". dirname($_SERVER['REQUEST_URI']));
}