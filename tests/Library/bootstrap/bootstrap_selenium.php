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

if (INSTALLSHOP) {
    $oCurl = new oxTestCurl();
    $oCurl->setUrl(shopURL . '/Services/_db.php');
    $oCurl->setParameters(array(
        'serial' => TEST_SHOP_SERIAL,
        'addDemoData' => 1,
        'turnOnVarnish' => OXID_VARNISH,
        'setupPath' => SHOP_SETUP_PATH,
    ));
    $sResponse = $oCurl->execute();
}

$oServiceCaller = new oxServiceCaller();
$oServiceCaller->setParameter('cl', 'oxConfig');
$oServiceCaller->setParameter('fnc', 'getEdition');
$edition = $oServiceCaller->callService('ShopObjectConstructor', 1);
define("SHOP_EDITION", ($edition == 'EE') ? 'EE' : 'PE_CE');

require_once TEST_LIBRARY_PATH . '/test_config.inc.php';

require_once TEST_LIBRARY_PATH . 'vendor/autoload.php';

require_once TESTS_DIRECTORY . '/acceptance/oxTestCase.php';

define('hostUrl', getenv('SELENIUM_SERVER')? getenv('SELENIUM_SERVER') : $sSeleniumServerIp );
define('browserName', getenv('BROWSER_NAME')? getenv('BROWSER_NAME') : $sBrowserName );

$sShopUrl = getenv('SELENIUM_TARGET')? getenv('SELENIUM_TARGET') : $sShopUrl;

define ( 'SELENIUM_SCREENSHOTS_PATH', getenv('SELENIUM_SCREENSHOTS_PATH')? getenv('SELENIUM_SCREENSHOTS_PATH') : $sSeleniumScreenShotsPath );
define ( 'SELENIUM_SCREENSHOTS_URL', getenv('SELENIUM_SCREENSHOTS_URL')? getenv('SELENIUM_SCREENSHOTS_URL') : $sSeleniumScreenShotsUrl );

if (SELENIUM_SCREENSHOTS_PATH && !is_dir(SELENIUM_SCREENSHOTS_PATH)) {
    mkdir(SELENIUM_SCREENSHOTS_PATH, 0777, 1);
}

if (getenv('OXID_LOCALE') == 'international') {
    define('oxTESTSUITEDIR', 'acceptanceInternational');
} else {
    define('oxTESTSUITEDIR', 'acceptance');
}
