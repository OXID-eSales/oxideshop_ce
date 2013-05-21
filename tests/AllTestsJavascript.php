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


// browser name which will be used for testing. Possible values: *iexplore, *iehta, *firefox, *chrome, *piiexplore, *pifirefox, *safari, *opera
// make sure that path to browser executable is known for the system
define('browserName', '*firefox' );

// URL to testible eShop
define('shopURL', getenv('SELENIUM_TARGET'));
define('hostUrl', getenv('SELENIUM_SERVER'));
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 *  implementation for adding and testing all javascript
 */

class javascript_javascriptSeleniumTest  extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;


     /**
     * construct
     *
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array())
    {

        $this->screenshotUrl  = getenv('SELENIUM_SCREENSHOTS_URL');

        $this->screenshotPath = getenv('SELENIUM_SCREENSHOTS_PATH');

        parent::__construct($name, $data, $dataName, $browser);
    }

     /**
     * preparing test enviroment
     *
     */
    protected function setUp()
    {
        parent::setUp();

        try {
            if (is_string(hostUrl)) {
                $this->setHost( hostUrl );
            }
            $this->setBrowser(browserName);
            $this->setBrowserUrl(shopURL);
            $this->setTimeout(60000);
//disabled temporarily till will be fixed ff windowns opening for every test
//            $this->setAutoStop(false);
        } catch (Exception $e) {
            $this->stopTesting("Failed preparing testing environment! Reason: ".$e->getMessage());
        }
    }

    /**
     * Finish selenium test
     *
     */
    public function tearDown()
    {

        echo "Closing active browser windows: ";
        parent::tearDown();

    }

    /**
     * Selenium test for all javascript qunit test
     *
     */
    public function testJavascript()
    {
        $this->open(shopURL);

        $this->waitForVisible("//p[@id='qunit-testresult']");
        $result =  $this->getText("//p[@id='qunit-testresult']/span[3]");

        $this->assertEquals($result, '0');


    }

}
