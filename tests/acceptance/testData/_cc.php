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
 * @version   SVN: $Id: _cc.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * This script unsets all domain cookies
 */

if (isset($_SERVER['HTTP_COOKIE'])) {
    $aCookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach ($aCookies as $sCookie) {
        $sRawCookie = explode('=', $sCookie);
        setcookie(trim( $sRawCookie[0] ), '', time() - 10000, '/');
    }
}

// also clean tmp dir
class _config {
    function __construct(){
        if (file_exists('_version_define.php')) {
            include "_version_define.php";
        }
        include "config.inc.php";
        include "config.inc.php";
    }
}

$_cfg = new _config();

foreach (glob($_cfg->sCompileDir."/*") as $filename) {
    if (is_file($filename)){
        unlink($filename);
    }
    if (is_dir($filename)){
        rmdir($filename);
    }
}


header("Location: ".dirname($_SERVER['REQUEST_URI']));
