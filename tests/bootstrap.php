<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: $
 */

if (getenv('TRAVIS_ERROR_LEVEL')) {
    error_reporting((int)getenv('TRAVIS_ERROR_LEVEL'));
} else {
    error_reporting((E_ALL ^ E_NOTICE) | E_STRICT);
}

ini_set('display_errors', true);

$sTestType = substr(getcwd(), strlen(__DIR__)+1);

define('TESTS_DIRECTORY', rtrim(__DIR__, '/').'/');

chdir(TESTS_DIRECTORY);

define('TEST_LIBRARY_PATH', rtrim(realpath('Library'), '/').'/');

if ($sTestType && strpos($sTestType, '/')) {
    $sTestType = substr($sTestType, 0, strpos($sTestType, '/'));
}

if (empty($sTestType)) {
    $sTestType = basename(end($_SERVER['argv']));
    $sTestType = str_replace('.php', '', $sTestType);
    $sTestType = strtolower(substr($sTestType, 8));
    reset($_SERVER['argv']);
}

require_once TEST_LIBRARY_PATH."bootstrap/bootstrap_config.php";
require_once TEST_LIBRARY_PATH."bootstrap/bootstrap_base.php";

if ($sTestType == 'selenium') {
    $sTestType = 'acceptance';
}

switch($sTestType) {
    case 'acceptance':
    case 'javascript':
        include_once TEST_LIBRARY_PATH."bootstrap/bootstrap_selenium.php";
        break;
    default:
        include_once TEST_LIBRARY_PATH."bootstrap/bootstrap_unit.php";
        break;
}
