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
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
//require_once 'test_config.inc.php';

class oxidAdditionalSeleniumFunctions extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;

    protected $screenshotPath = null;
    protected $screenshotUrl  = null;

    /**
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     * @param  array  $browser
     * @throws InvalidArgumentException
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '', array $browser = array())
    {
        $this->screenshotUrl  = getenv('SELENIUM_SCREENSHOTS_URL');
        $this->screenshotPath = getenv('SELENIUM_SCREENSHOTS_PATH');

        parent::__construct($name, $data, $dataName, $browser);
    }

//---------------------------- general functions --------------------------------

    /**
     * Sets up default environment for tests
     *
     * @param bool $skipDemoData
     */
    protected function setUp($skipDemoData=false)
    {
        try {
            if (is_string(hostUrl)) {
                $this->setHost( hostUrl );
            }
            $this->setBrowser(browserName);
            $this->setBrowserUrl(shopURL);

        } catch (Exception $e) {
            $this->stopTesting("Failed preparing testing environment! Reason: ".$e->getMessage());
        }
    }

   /**
     * Restores database after every test
     *
     */
    protected function tearDown()
    {
        $this->restoreDB();

        parent::tearDown();
    }

    /**
     * adds some demo data to database
     */
    public function addDemoData($demo=demoData)
    {
        if (filesize($demo)) {
            $myConfig = oxConfig::getInstance();

            $sUser    = $myConfig->getConfigParam( 'dbUser' );
            $sPass    = $myConfig->getConfigParam( 'dbPwd' );
            $sDbName  = $myConfig->getConfigParam( 'dbName' );
            $sHost    = $myConfig->getConfigParam( 'dbHost' );
            $sCmd = 'mysql -h'.escapeshellarg($sHost).' -u'.escapeshellarg($sUser).' -p'.escapeshellarg($sPass).' --default-character-set=utf8 '.escapeshellarg($sDbName).'  < '.escapeshellarg($demo).' 2>&1';
            exec($sCmd, $sOut, $ret);
            $sOut = implode("\n",$sOut);
            if ( $ret > 0 ) {
                throw new Exception( $sOut );
            }
        }
    }

    /**
     * deletes all files in tmp dir
     */
    public function clearTmp()
    {
        $this->open(shopURL."_deleteTmp.php");
    }

    /**
     * opens shop frontend and runs checkForErrors()
     *
     */
    public function openShop()
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->checkForErrors();
    }

    /**
     * tests if none of php possible errors are displayed into shop frontend page
     */
    public function checkForErrors()
    {
        if (($this->isTextPresent("Warning: "))  ||  ($this->isTextPresent("ADODB_Exception"))||
            ($this->isTextPresent("Fatal error: "))  || ($this->isTextPresent("Catchable fatal error: ")) ||
            ($this->isTextPresent("Notice: "))  || ($this->isTextPresent("exception '")) ||
            ($this->isTextPresent("ERROR : Tran")) ||($this->isTextPresent("does not exist or is not accessible!")) ||
            ($this->isTextPresent("EXCEPTION_")) )   {

             $this->refresh();
         }
        $this->assertFalse($this->isTextPresent("Warning: "), "PHP Warning is in the page");
        $this->assertFalse($this->isTextPresent("ADODB_Exception"), "ADODB Exception is in the page");
        $this->assertFalse($this->isTextPresent("Fatal error: "), "PHP Fatal error is in the page");
        $this->assertFalse($this->isTextPresent("Catchable fatal error: "), " Catchable fatal error is in the page");
        $this->assertFalse($this->isTextPresent("Notice: "), "PHP Notice is in the page");
        $this->assertFalse($this->isTextPresent("exception '"), "Uncaught exception is in the page");
        $this->assertFalse($this->isTextPresent("does not exist or is not accessible!"), "Warning about not existing function is in the page ");
        $this->assertFalse($this->isTextPresent("ERROR : Tran"), "Missing translation for constant (ERROR : Translation for...)");
        $this->assertFalse($this->isTextPresent("EXCEPTION_"), "Exception - component not found (EXCEPTION_)");
    }

    /**
     * removes \n signs and it leading spaces from string. keeps only single space in the ends of each row
     *
     * @param string $sLine not formatted string (with spaces and \n signs)
     *
     * @return string formatted string with single spaces and no \n signs
     */
    public function clearString( $sLine )
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $sLine));
    }

    /**
     * clicks link/button and waits till page will be loaded. then checks for errors.
     * recommended to use in frontend. use in admin only, if this click wont relode frames
     *
     * @param string $locator link/button locator in the page
     * @param string $element element locator for additional check if page is fully loaded (optional)
     */
    public function clickAndWait($locator, $element=null)
    {
        if (!$this->isElementPresent($locator)) {
            $this->waitForElement($locator);
        }
        $this->click($locator);
        $this->waitForPageToLoad("90000");
        //additional check if page is really loaded. on demand only for places, that have this problem
        if ($element) {
            sleep(1);
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

    /**
     * selects label in selectlist and waits till page will be loaded. then checks for errors.
     * recommended to use in frontend. use in admin only, if this select wont relode frames
     *
     * @param string $locator selectlist locator
     * @param string $selection   option to select
     * @param string $element element locator for additional check if page is fully loaded (optional)
     */
    public function selectAndWait($locator, $selection, $element=null)
    {
        if (!$this->isElementPresent($locator)) {
            $this->waitForElement($locator);
        }
        $this->select($locator, $selection);
        $this->waitForPageToLoad("90000");
        //additional check if page is really loaded. on demand only for places, that have this problem
        if ($element) {
            sleep(1);
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

    /**
     * waits till element will appear in page (only IF such element DID NOT EXIST BEFORE)
     *
     * @param $element element locator
     */
    public function waitForElement($element)
    {
        $refreshed = false;
        for ($second = 0; $second <= 30; $second++) {
            if ($second >= 30 && !$refreshed) {
                $refreshed = true;
                $second = 0;
                $this->refresh();
            } else if ($second >= 30) {
                $this->assertTrue($this->isElementPresent($element), "timeout while waiting for element ".$element);
            }
            try {
                if ($this->isElementPresent($element)) {
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
    }

    /**
     * waits for element to show up (only IF such element ALREADY EXIST AS HIDDEN AND WILL BE SHOWN AS VISIBLE)
     *
     * @param string $locator element locator
     */
    public function waitForItemAppear( $locator )
    {
        for ($second = 0; ; $second++) {
            if ($second >= 90) {
                $this->assertTrue($this->isElementPresent($locator), "timeout waiting for element ".$locator);
                $this->assertTrue($this->isVisible($locator), "element ".$locator." is not visible");
            }
            try {
                if ($this->isElementPresent($locator)) {
                    if ($this->isVisible($locator)) {
                        break;
                    }
                }
            } catch (Exception $e) {echo $e->getMessage();}
            sleep(1);
        }
    }

    /**
     * waits for element to disappear (only IF such element WILL BE MARKED AS NOT VISIBLE)
     *
     * @param string $locator element locator
     */
    public function waitForItemDisappear( $locator )
    {
        for ($second = 0; ; $second++) {
            if ($second >= 90) {
                $this->assertFalse($this->isVisible($locator), "timeout. Element ".$locator." still visible");
            }
            try {
                if (!$this->isVisible($locator)) {
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
    }

    /**
     * Waits till text will appear in page. If array is passed, waits for any of texts in array to appear
     *
     * @param string $textMsg text
     * @param bool $printSource print source (default false)
     * @param int $timeout timeout (default 90)
     */
    public function waitForText($textMsg, $printSource = false, $timeout=90 )
    {
        if (is_array($textMsg)) {
            $aMsg = $textMsg;
        } else {
            $aMsg[] = $textMsg;
        }
        for ($second = 0; ; $second++) {
            if ($second >= $timeout) {
                if ($printSource) {
                    echo "<hr> ".$this->getHtmlSource()." <hr>";
                }
                $this->assertTrue(false, "Timeout while waiting for text: ".implode(' | ', $aMsg));
                break;
            }
            try {
                foreach ($aMsg as $textLine) {
                    if ($this->isTextPresent($textLine)) {
                        break 2;
                    }
                }
            } catch (Exception $e) {}
            sleep(1);
        }
    }

    /**
     * waits till text will disappear from page
     *
     * @param string $textLine text
     */
    public function waitForTextDisappear( $textLine )
    {
        for ($second = 0; ; $second++) {
            if ($second >= 90) {
                $this->assertFalse($this->isTextPresent($textLine), "timeout. Text ".$textLine. " still visible");
            }
            try {
                if (!$this->isTextPresent($textLine)) {
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
    }

//---------------------------------- Admin side only functions --------------------------

    /**
     * logins to admin with default admin pass and opens needed menu
     *
     * @param string $menuLink1     menu link (e.g. master settings, shop settings)
     * @param string $menuLink2     submenu link (e.g. administer products, discounts, vat)
     * @param string $editElement   element to check in edit frame (optional)
     * @param string $listElement   element to check in list frame (optional)
     * @param bool   $forceMainShop force main shop
     * @param string $user          shop admin username
     * @param string $pass          shop admin password
     */
    public function loginAdmin($menuLink1, $menuLink2, $editElement=null, $listElement=null, $forceMainShop=false, $user="admin@myoxideshop.com", $pass="admin0303")
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->open(shopURL."admin");
        $this->checkForErrors();
        $this->assertTrue($this->isElementPresent("user"), "Admin login page failed to load");
        $this->assertTrue($this->isElementPresent("pwd"), "Admin login page failed to load");
        $this->type("user", $user);
        $this->type("pwd", $pass);
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->waitForElement("link=".$menuLink1);
        $this->checkForErrors();
        $this->click("link=".$menuLink1);
        $this->clickAndWaitFrame("link=".$menuLink2, "edit");
        //testing edit frame for errors
        $this->frame("edit", $editElement);
        //testing list frame for errors
        $this->frame("list", $listElement);
    }

    /**
     * logins to admin for paypal shop with admin pass and opens needed menu
     *
    * @param string $menuLink1     menu link (e.g. master settings, shop settings)
    * @param string $menuLink2     submenu link (e.g. administer products, discounts, vat)
    * @param string $editElement   element to check in edit frame (optional)
    * @param string $listElement   element to check in list frame (optional)
    * @param bool   $forceMainShop force main shop
    * @param string $user          shop admin username
    * @param string $pass          shop admin password
    */
    public function loginAdminForModule($menuLink1, $menuLink2, $editElement=null, $listElement=null, $forceMainShop=false, $user="admin", $pass="admin")
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."/_cc.php");
        $this->open(shopURL."/admin");
        $this->checkForErrors();
        $this->assertTrue($this->isElementPresent("user"), "Admin login page failed to load");
        $this->assertTrue($this->isElementPresent("pwd"), "Admin login page failed to load");
        $this->type("user", $user);
        $this->type("pwd", $pass);
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");


        $this->waitForElement("link=".$menuLink1);
        $this->checkForErrors();
        $this->click("link=".$menuLink1);
        $this->clickAndWaitFrame("link=".$menuLink2, "edit");

        //testing edit frame for errors
        $this->frame("edit", $editElement);

        //testing list frame for errors
        $this->frame("list", $listElement);
    }

    /**
     * selects other menu in admin interface
     *
     * @param string $menuLink1   menu link (e.g. master settings, shop settings)
     * @param string $menuLink2   submenu link (e.g. administer products, discounts, vat)
     * @param string $editElement element to check in edit frame (optional)
     * @param string $listElement element to check in list frame (optional)
     */
    public function selectMenu($menuLink1, $menuLink2, $editElement=null, $listElement=null)
    {
        $this->selectWindow(null);
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->waitForElement("link=".$menuLink1);
        $this->checkForErrors();
        $this->click("link=".$menuLink1);
        $this->clickAndWaitFrame("link=".$menuLink2, "edit");

        //testing edit frame for errors
        $this->frame("edit", $editElement);

        //testing list frame for errors
        $this->frame("list", $listElement);
    }

    /**
     * select frame in Admin interface
     *
     * @param string $frameLocator name of needed admin frame.
     * @param string $frameElement name of element to check (optional)
     */
    public function frame($frameLocator, $frameElement=null)
    {
        $this->selectFrame("relative=top");
        $this->selectFrame("basefrm");

        $this->waitForElement($frameLocator);

        $this->selectFrame($frameLocator);

        if ($frameElement && $frameElement != "") { //additional checking if element is loaded (optional)
            sleep(1);
            $this->waitForElement($frameElement);
        }
        $this->checkForErrors();
    }

    /**
     * selects element and waits till needed frame will be loaded. same frame as before will be selected
     *
     * @param string $locator   selectlist locator
     * @param string $selection option to select
     * @param string $frame     frame wich should be also loaded (this frame will be loaded after current frame is loaded)
     * @param string $element   element locator for additional check if page is fully loaded (optional)
     */
    public function selectAndWaitFrame($locator, $selection, $frame, $element=null)
    {
        if (!$this->isElementPresent($locator)) {
            $this->waitForElement($locator);
        }
        $this->select($locator, $selection);
        $this->waitForFrameToLoad($frame);
        sleep(1);
        if ($element) {
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

    /**
     * selects element and waits till needed frame will be loaded. same frame as before will be selected
     *
     * @param string $locator selectlist locator
     * @param string $frame   frame wich should be also loaded (this frame will be loaded after current frame is loaded)
     * @param string $element element locator for additional check if page is fully loaded (optional)
     * @param int    $sleep   seconds to wait, default 1
     */
    public function clickAndWaitFrame($locator, $frame, $element=null, $sleep = 1)
    {
        if (!$this->isElementPresent($locator)) {
            $this->waitForElement($locator);
        }
        $this->setTimeout(90);
        $this->click($locator);
        $this->waitForFrameToLoad($frame);
        sleep($sleep);
        if ($element) {
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

    /**
     * clicks entered link in list frame and selects edit frame.
     *
     * @param string $linkLocator    link name or tab name that is presed
     * @param string $elementLocator locator for element which will be checked for page loading success (optional)
     * @param int    $sleep          seconds to wait, default 1
     */
    public function openTab($linkLocator, $elementLocator="btn.help", $sleep = 1)
    {
        $frameLocator="edit";
        $this->click($linkLocator);
        $this->waitForFrameToLoad($frameLocator);
        sleep($sleep);
        $this->waitForElement($linkLocator);
        $this->assertTrue($this->isElementPresent($linkLocator), "problems with reloading frame. Element ".$linkLocator." not found in it.");
        $this->checkForErrors();
        $this->frame($frameLocator, $elementLocator);
    }

    /**
     * click button and confirms dialog
     *
     * @param string $locator locator for delete button
     * @param string $element locator for element which will be checked for page loading success. default value submitit
     * @param string $frame   frame wich should be also loaded (this frame will be loaded after current frame is loaded)
     */
    public function clickAndConfirm($locator, $element=null, $frame="edit")
    {
        if (!$this->isElementPresent($locator)) {
            $this->waitForElement($locator);
        }
        $this->click($locator);
        sleep(1);
        $this->getConfirmation();
        $this->waitForFrameToLoad($frame);
        if ($element) {
            sleep(1);
            $this->waitForElement($element);
        }
        $this->checkForErrors();
    }

//---------------------------- Ajax functions for admin side ------------------------------------------------

    /**
     * selects popUp window and waits till it is fully loaded
     *
     * @param string $popUpElement element used to check if popUp is fully loaded
     */
    public function usePopUp($popUpElement="//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]")
    {
        $this->waitForPopUp("ajaxpopup", "90000");
        $this->selectWindow("name=ajaxpopup");
        $this->windowMaximize("name=ajaxpopup");
        $this->waitForElement($popUpElement);
        sleep(1);
        $this->checkForErrors();
    }

    /**
     * waits for element to show up in specific place
     *
     * @param stirng $value   expected text to show up
     * @param string $locator place where specified text must show up
     */
    public function waitForAjax( $value, $locator )
    {
        for ($second = 0; ; $second++) {
            if ($second >= 90) {
                $this->assertTrue($this->isElementPresent($locator), "Ajax timeout");
                $this->assertEquals($value, $this->getText($locator));
            }
            try {
                if ($this->isElementPresent($locator)) {
                    if ($value == $this->getText($locator)) {
                        return ;
                    }
                }
            } catch (Exception $e) {}
            sleep(1);
        }
        $this->checkForErrors();
    }

    /**
     * drags and drops element to specified location
     *
     * @param stirng $item      element which will be dragged and dropped
     * @param string $container place where to drop specified element
     */
    public function dragAndDrop($item, $container)
    {
        $this->click($item);
        $this->checkForErrors();
        $this->dragAndDropToObject($item, $container);
    }

//------------------------ Subshop related functions ----------------------------------------

    /**
     * logins to admin with admin pass, selects subshop and opens needed menu
     *
     * @param string $menuLink1 menu link (e.g. master settings, shop settings)
     * @param string $menuLink2 submenu link (e.g. administer products, discounts, vat)
     */
    public function loginSubshopAdmin($menuLink1, $menuLink2)
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->open(shopURL."admin");
        $this->checkForErrors();
        $this->type("user","admin@myoxideshop.com");
        $this->type("pwd","admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->selectAndWaitFrame("selectshop", "label=subshop", "edit");
        $this->waitForElement("link=".$menuLink1);
        $this->checkForErrors();
        $this->click("link=".$menuLink1);
        $this->clickAndWaitFrame("link=".$menuLink2, "edit");
        //testing edit frame for errors
        $this->frame("edit");
        //testing list frame for errors
        $this->frame("list");
    }

    /**
     * opens subshop frontend and switch to EN language
     */
    public function openSubshopFrontend()
    {
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->clickAndWait("link=subshop");
        $this->checkForErrors();
    }

//---------------------------- Setup related functions ------------------------------
    /**
     * prints error message, closes active browsers windows and stops
     *
     * @param string    $sErrorMsg       message to display about error place (more easy to find for programmers)
     * @param Exception $oErrorException Exception to throw on error
     */
    public function stopTesting($sErrorMsg, $oErrorException = null)
    {
        if ($oErrorException) {
            try {
                $this->onNotSuccessfulTest($oErrorException);
            } catch (Exception $oE) {
                if ($oE instanceof PHPUnit_Framework_ExpectationFailedException) {
                    $sErrorMsg .= "\n\n---\n".$oE->getCustomMessage();
                }
            }
        }
        echo $sErrorMsg;
        echo " Selenium tests terminated.";
        $this->stop();
        exit(1);
    }

//----------------------------- eFire modules for shop ------------------------------------
    /**
     * downloads efire connector
     *
     * @param string $sNameEfi user name for eFire
     * @param string $sPswEfi  user password for eFire
     * @param string $user     user name for login to shop admin
     * @param string $pass     user password for login to shop admin
     */
    public function downloadConnector($sNameEfi, $sPswEfi, $user="admin@myoxideshop.com", $pass="admin0303")
    {
        /*
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->open(shopURL."admin");
        $this->checkForErrors();
        $this->type("user", $user);
        $this->type("pwd", $pass);
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        */
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->waitForElement("link=OXID eFire");
        $this->checkForErrors();
        $this->click("link=OXID eFire");
        $this->clickAndWaitFrame("link=Shop connector", "edit");

        //testing edit frame for errors
        $this->frame("edit");
        $this->assertFalse($this->isTextPresent("*connector downloaded successfully"));
        $this->type("etUsername", $sNameEfi);
        $this->type("etPassword", $sPswEfi);
        $this->clickAndWait("etSubmit");
        $this->assertTrue($this->isTextPresent("*connector downloaded successfully"), "connector was not downloaded successfully");
        $this->clearTmp();
        echo " connector downloaded successfully. ";
    }

//----------------------------- new templates for eShop frontend ------------------------------------
    /**
     * logins customer by using login flyout form
     *
     * @param string $userName user name (email)
     * @param string $userPass user password
     */
    public function loginInFrontend($userName, $userPass)
    {
        $this->selectWindow(null);
        $this->click("//ul[@id='topMenu']/li[1]/a");
        $this->waitForItemAppear("loginBox");
        $this->type("//div[@id='loginBox']//input[@name='lgn_usr']", $userName);
        $this->type("//div[@id='loginBox']//input[@name='lgn_pwd']", $userPass);
        $this->clickAndWait("//div[@id='loginBox']//button[@type='submit']");
    }

    /**
     * mouseOver element and then click specified link
     *
     * @param string $element1 mouseOver element
     * @param string $element2 clickable element
     */
    public function mouseOverAndClick($element1, $element2)
    {
        $this->mouseOver($element1);
        $this->waitForItemAppear($element2);
        $this->clickAndWait($element2);
    }

    /**
     * performs search for selected parameter
     *
     * @param string $searchParam search parameter
     */
    public function searchFor($searchParam)
    {
        $this->type("//input[@id='searchParam']", $searchParam);
        $this->keyPress("searchParam", "\\13"); //presing enter key
        $this->waitForPageToLoad();
        $this->checkForErrors();
    }

    /**
     * opens basket
     *
     * @param string $language  active language in shop
     */
    public function openBasket($language="English")
    {
        if ($language == 'Deutsch') {
            $sLink = "Warenkorb zeigen";
        } else {
            $sLink = "Display Cart";
        }
        $this->click("//div[@id='miniBasket']/img");
        $this->waitForItemAppear("//div[@id='basketFlyout']//a[text()='".$sLink."']");
        $this->clickAndWait("//div[@id='basketFlyout']//a[text()='".$sLink."']");
    }

    /**
     * selects specified value from dropdown (sorting, items per page etc)
     *
     * @param int    $elementId  drop down element id
     * @param string $itemValue  item to select
     * @param string $extraIdent additional identificator for element
     */
    public function selectDropDown($elementId, $itemValue='', $extraIdent='')
    {
        $this->assertTrue($this->isElementPresent($elementId));
        $this->assertFalse($this->isVisible("//div[@id='".$elementId."']//ul"));
        $this->click("//div[@id='".$elementId."']//p");
        $this->waitForItemAppear("//div[@id='".$elementId."']//ul");
        if ('' == $itemValue) {
            $this->clickAndWait("//div[@id='".$elementId."']//ul/".$extraIdent."/a");
        } else {
            $this->clickAndWait("//div[@id='".$elementId."']//ul/".$extraIdent."/a[text()='".$itemValue."']");
        }
    }

    /**
     * selects specified value from dropdown (for multidimensional variants)
     *
     * @param string $elementId  container id
     * @param int    $elementNr  select list number (e.g. 1, 2)
     * @param string $itemValue  item to select
     * @param string $textMsg    text that must appear after selecting md variant
     */
    public function selectVariant($elementId, $elementNr, $itemValue, $textMsg='')
    {
        $this->assertTrue($this->isElementPresent($elementId));
        $this->assertFalse($this->isVisible("//div[@id='".$elementId."']/div[".$elementNr."]//ul"));
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//p");
        $this->waitForItemAppear("//div[@id='".$elementId."']/div[".$elementNr."]//ul");
        $this->click("//div[@id='".$elementId."']/div[".$elementNr."]//ul//a[text()='".$itemValue."']");
        if (!empty($textMsg)) {
            $this->waitForText($textMsg);
        } else {
            $this->waitForPageToLoad("90000");
        }
    }

    /**
     * executes given sql. for EE version cash is also cleared
     *
     * @param string $sql  sql line
     */
    public function executeSql($sql)
    {
        oxDb::getDb()->execute($sql);
    }

    /**
     * gets clean heading text without any additional info as rss labels and so.
     *
     * @param string $element path to element
     *
     * @return string
     */
    public function getHeadingText($element)
    {
        $text = $this->getText($element);
        if ($this->isElementPresent($element."/a")) {
            $search = $this->getText($element."/a");
            $text = str_replace($search, "", $text);
        }
        return trim($text);
    }

    /**
     * selects esho language in frontend
     *
     * @param string $language language title
     */
    public function switchLanguage($language)
    {
        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->click("//ul[@id='languages']//li/a/span[text()='".$language."']");
        $this->waitForItemDisappear("languages");
    }

    // --------------------------- trusted shops ------------------------------

    /**
     * logins to trusted shops in admin
     *
     * @param string $link1
     * @param string $link2
     */
    public function loginAdminTs($link1 = "link=Seal of quality", $link2 = "link=Trusted Shops")
    {
        oxDb::getInstance()->getDb()->Execute("UPDATE `oxconfig` SET `OXVARVALUE` = 0xce92 WHERE `OXVARNAME` = 'sShopCountry';");
        $this->selectWindow(null);
        $this->windowMaximize(null);
        $this->selectFrame("relative=top");
        $this->open(shopURL."_cc.php");
        $this->open(shopURL."admin");
        $this->checkForErrors();
        $this->type("user", "admin@myoxideshop.com");
        $this->type("pwd", "admin0303");
        $this->select("chlanguage", "label=English");
        $this->select("profile", "label=Standard");
        $this->click("//input[@type='submit']");
        $this->waitForElement("nav");
        $this->selectFrame("relative=top");
        $this->selectFrame("navigation");
        $this->waitForElement($link1);
        $this->checkForErrors();
        $this->click($link1);
        $this->clickAndWaitFrame($link2, "edit");

        //testing edit frame for errors
        $this->frame("edit");
    }

    /**
     * Checks which tables of the db changed and then restores these tables.
     *
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     *
     * @param string $sTmpPrefix temp file name
     *
     * @throws Exception on error while restoring db
     *
     * @return null
     */
    public function restoreDB( $sTmpPrefix = null)
    {
        $time = microtime(true);
        //var_Dump("Restore: ".number_format(memory_get_usage(), 0, '.', ','));
        $myConfig = oxConfig::getInstance();

        $sUser    = $myConfig->getConfigParam( 'dbUser' );
        $sPass    = $myConfig->getConfigParam( 'dbPwd' );
        $sDbName  = $myConfig->getConfigParam( 'dbName' );
        $sHost    = $myConfig->getConfigParam( 'dbHost' );
        if (!$sTmpPrefix) {
            $sTmpPrefix = 'tmp_db_dump';
        }
        $demo = '/tmp/'.$sTmpPrefix.'_'.$sDbName;

        $sCmd = 'mysql -h'.escapeshellarg($sHost).' -u'.escapeshellarg($sUser).' -p'.escapeshellarg($sPass).' --default-character-set=utf8 '.escapeshellarg($sDbName).'  < '.escapeshellarg($demo).' 2>&1';
        exec($sCmd, $sOut, $ret);
        $sOut = implode("\n",$sOut);
        if ( $ret > 0 ) {
            throw new Exception( $sOut );
        }

       // echo("Restore end: ".number_format(memory_get_usage(), 0, '.', ','));
       // echo(" T:".(microtime(true)-$time));
    }

    /**
     * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
     * the dump includes the data and sql insert statements
     *
     * @param string $sTmpPrefix temp file name
     *
     * @throws Exception on error while dumping
     *
     * @return null
     */
    public function dumpDB( $sTmpPrefix = null )
    {
        $time = microtime (true);
       // echo("Dump: ".number_format(memory_get_usage(), 0, '.', ','));
        $myConfig = oxConfig::getInstance();

        $sUser    = $myConfig->getConfigParam( 'dbUser' );
        $sPass    = $myConfig->getConfigParam( 'dbPwd' );
        $sDbName  = $myConfig->getConfigParam( 'dbName' );
        $sHost    = $myConfig->getConfigParam( 'dbHost' );
        if (!$sTmpPrefix) {
            $sTmpPrefix = 'tmp_db_dump';
        }
        $demo = '/tmp/'.$sTmpPrefix.'_'.$sDbName;

        $sCmd = 'mysqldump -h'.escapeshellarg($sHost).' -u'.escapeshellarg($sUser).' -p'.escapeshellarg($sPass).' --add-drop-table '.escapeshellarg($sDbName).'  > '.escapeshellarg($demo);
        exec($sCmd, $sOut, $ret);
        $sOut = implode("\n",$sOut);
        if ( $ret > 0 ) {
            throw new Exception( $sOut );
        }
        //echo("Dump end: ".number_format(memory_get_usage(), 0, '.', ','));
        echo("db Dumptime: ".(microtime (true)-$time)."\n");
    }

    /**
     * Call shop seleniums connector to execute code in shop.
     * @example call to update information to database.
     *
     * @param string $sCl class name.
     * @param string $sFnc function name.
     * @param string $sOxid id of object.
     * @param array  $aClassParams params to set to object.
     * @param string $sShopId object shop id.
     *
     * @return void
     */
    public function callShopSC($sCl, $sFnc, $sOxid = null, $aClassParams = array(), $sShopId = null)
    {
        $oConfig = oxConfig::getInstance();

        $sShopUrl = $oConfig->getShopMainUrl() . '/_sc.php';
        $sClassParams = '';
        foreach ($aClassParams as $sParamKey => $sParamValue) {
            if (is_array($sParamValue)) {
                foreach ($sParamValue as $sSubParamKey => $sSubParamValue) {
                    $sSubParamValue = urlencode($sSubParamValue);
                    $sClassParams = $sClassParams ."&". "classparams[". $sParamKey ."][".$sSubParamKey."]=". $sSubParamValue;
                }
            } else {
                $sParamValue = urlencode($sParamValue);
                $sClassParams = $sClassParams ."&". "classparams[". $sParamKey ."]=". $sParamValue;
            }
        }
        $sParams = "?cl=". $sCl ."&fnc=". $sFnc
            . (!empty($sOxid) ? ("&oxid=". $sOxid) : "")
            . (!empty($sClassParams) ? $sClassParams : "");


        // Pass shopId as to change in different shop we need to make it active.
        if ($sShopId) {
            $sParams .= "&shp=".$sShopId;
        } else {
            $sParams .= "&shp=".oxSHOPID;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sShopUrl . $sParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cache-Control: no-cache;'));

        curl_setopt( $ch, CURLOPT_USERAGENT, "OXID-SELENIUMS-CONNECTOR" );
        $sRes = curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Return main shop number.
     * To use to form link to main shop and etc.
     *
     * @return string
     */
    public function getShopVersionNumber()
    {
        return '4';
    }

    /**
     * Overrides original method - additionaly can check
     * is text present by parsing text according given path
     *
     * @param string $sText text to be searched
     * @param string $sPath text path
     *
     * @return bool
     */
    public function isTextPresent($sText, $sPath=null)
    {
        if ( $sPath ) {
            $sParsedText = $this->getText( $sPath );
            return ( strpos( $sParsedText, $sText) !== false );
        } else {
            return parent::isTextPresent( $sText );
        }
    }
}