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
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__FILE__) . "/../bootstrap.php";
require_once 'ServiceCaller.php';
require_once 'ShopServiceInterface.php';

define('LIBRARY_PATH', dirname(__FILE__).'/Library/');
define('TEMP_PATH', dirname(__FILE__).'/temp/');
define('SHOP_PATH', dirname(__FILE__) . '/../');

if (!file_exists(TEMP_PATH)) {
    mkdir(TEMP_PATH, 0777);
    chmod(TEMP_PATH, 0777);
}

try {
    $oxConfig = oxRegistry::getConfig();

    $oServiceCaller = new ServiceCaller();

    $oServiceCaller->setActiveShop($oxConfig->getRequestParameter('shp'));
    $oServiceCaller->setActiveLanguage($oxConfig->getRequestParameter('lang'));
    $mResponse = $oServiceCaller->callService($oxConfig->getRequestParameter('service'));

    echo serialize($mResponse);
} catch (Exception $e) {
    echo "EXCEPTION: ".$e->getMessage();
}
