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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: $
 */

$sTestType = substr(getcwd(), strlen(__DIR__)+1);

chdir(__DIR__);

if ($sTestType && strpos($sTestType, '/')) {
    $sTestType = substr($sTestType, 0, strpos($sTestType, '/'));
}

if (empty($sTestType)) {
    $sTestType = strtolower(substr($_SERVER['argv'][4], 8));
}

require_once "bootstrap_config.php";

switch($sTestType) {
    case 'acceptance':
    case 'acceptanceEfire':
    case 'acceptanceInternational':
        include_once "bootstrap_selenium.php";
        break;
    default:
        include_once "bootstrap_unit.php";
        break;
}