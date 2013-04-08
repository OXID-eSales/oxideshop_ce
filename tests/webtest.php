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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

if (isset($_GET["testClass"],$_GET["testMethod"])) {
    $testClass  = htmlspecialchars($_GET["testClass"]);
    $testMethod = htmlspecialchars($_GET["testMethod"]);
}
?>
<html>
    <head>
        <title>WebTest for PHPUnit</title>
    </head>
    <body>
        <form action=webtest.php method=GET>
            <label for="testClass">Test class (e.g. unit_oxarticleTest):</label><br>
            <input size=50 type="text" value="<?php echo $testClass?$testClass:"unit_Test";?>" name=testClass><br>

            <label for="testMethod">Test:</label><br>
            <input type="text" value="<?php echo $testMethod;?>" name="testMethod" id="testMethod"><br>

            <input type="submit" value="Execute">
        </form>
    </body>
</html>
<?php

$sEShopDir = realpath ( dirname(__FILE__) . '/../');
$aArg = $_GET;
unset($aArg['XDEBUG_SESSION_START']);
$aArg = array_keys($aArg);

if (!$testClass) {
    exit();
}

$aArgs = array($sEShopDir.'/library/PHPUnit/phpunit.php', '--bootstrap',
    'bootstrap.php',
    '--verbose');

if ($testMethod) {
    $aArgs[] = "--filter";
    $aArgs[] = $testMethod;
}

$aArgs[] = $testClass;

$_SERVER['argv'] = $aArgs;

set_include_path(".:/htdocs/oxideshop/eshop/library/PHPUnit:/htdocs/oxideshop/eshop/library/:/htdocs/oxideshop/eshop/tests/");

echo "<pre>";
require $sEShopDir.'/library/PHPUnit/phpunit.php';
echo "</pre>";
