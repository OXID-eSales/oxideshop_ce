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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller;

use \oxDisplayError;

use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing GuestbookEntry class
 */
class GuestbookEntryTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->getSession()->setVariable("gbSessionFormId", null);
        $this->getSession()->setVariable("Errors", null);
        oxDb::getDB()->execute('delete from oxgbentries');

        parent::tearDown();
    }

    /**
     * Test get form id.
     *
     * @return null
     */
    public function testGetFormId()
    {
        oxTestModules::addFunction("oxUtilsObject", "generateUId", "{return 'xxx';}");

        $oView = oxNew('GuestbookEntry');
        $this->assertEquals('xxx', $oView->getFormId());
    }

    /**
     * Test save entry when user is not logged in.
     *
     * @return null
     */
    public function testSaveEntryNoSessionUser()
    {
        $this->getSession()->setVariable('usr', null);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oView = oxNew('GuestbookEntry');
        $this->assertNull($oView->saveEntry());

        $aErrors = oxRegistry::getSession()->getVariable("Errors");
        $this->assertTrue(isset($aErrors['default']));

        $oExcp = unserialize($aErrors['default'][0]);
        $this->assertTrue($oExcp instanceof oxDisplayError);
        $this->assertEquals(oxRegistry::getLang()->translateString("ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_LOGIN_TO_WRITE_ENTRY"), $oExcp->getOxMessage());
    }

    /**
     * Test save entry when unable to resolve shop id.
     *
     * @return null
     */
    public function testSaveEntryNoShopId()
    {
        $this->getSession()->setVariable('usr', 'xxx');

        /** @var oxConfig|PHPUnit_Framework_MockObject_MockObject $oConfig */
        $oConfig = $this->getMock("oxconfig", array("getShopId"));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(null));

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestbookEntry|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock("GuestbookEntry", array("init", "getConfig"));
        $oView->expects($this->any())->method('init');
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('guestbookentry', $oView->saveEntry());

        $aErrors = oxRegistry::getSession()->getVariable("Errors");
        $this->assertTrue(isset($aErrors['default']));

        $oExcp = unserialize($aErrors['default'][0]);
        $this->assertTrue($oExcp instanceof oxDisplayError);
        $this->assertEquals(oxRegistry::getLang()->translateString("ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_UNDEFINED_SHOP"), $oExcp->getOxMessage());
    }

    /**
     * Test save entry with empty review text.
     *
     * @return null
     */
    public function testSaveEntryNoReviewText()
    {
        $this->getSession()->setVariable('usr', 'xxx');
        $this->setRequestParameter('rvw_txt', null);

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oView = oxNew('GuestbookEntry');
        $this->assertEquals('guestbookentry', $oView->saveEntry());

        $aErrors = oxRegistry::getSession()->getVariable("Errors");
        $this->assertTrue(isset($aErrors['default']));

        $oExcp = unserialize($aErrors['default'][0]);
        $this->assertTrue($oExcp instanceof oxDisplayError);
        $this->assertEquals(oxRegistry::getLang()->translateString("ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_REVIEW_CONTAINS_NO_TEXT"), $oExcp->getOxMessage());
    }

    /**
     * Test save entry exceeding maximum saves, with flood protection on.
     *
     * @return null
     */
    public function testSaveEntryDeniedByFloodProtector()
    {
        oxTestModules::addFunction("oxgbentry", "floodProtection", "{return true;}");

        $this->getSession()->setVariable('usr', 'xxx');
        $this->setRequestParameter('rvw_txt', 'xxx');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oView = oxNew('GuestbookEntry');
        $this->assertEquals('guestbookentry', $oView->saveEntry());

        $aErrors = oxRegistry::getSession()->getVariable("Errors");
        $this->assertTrue(isset($aErrors['default']));

        $oExcp = unserialize($aErrors['default'][0]);
        $this->assertTrue($oExcp instanceof oxDisplayError);
        $this->assertEquals(oxRegistry::getLang()->translateString("ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_MAXIMUM_NUMBER_EXCEEDED"), $oExcp->getOxMessage());
    }

    /**
     * Test save entry with missmatched session id's.
     *
     * @return null
     */
    public function testSaveEntrySessionAndFormIdsDoesNotMatch()
    {
        $this->getSession()->setVariable('usr', 'xxx');
        $this->getSession()->setVariable('gbSessionFormId', 'xxx');

        $this->setRequestParameter('rvw_txt', 'xxx');
        $this->setRequestParameter('gbFormId', 'yyy');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oView = oxNew('GuestbookEntry');
        $this->assertEquals('guestbook', $oView->saveEntry());

        $this->assertEquals(0, oxDb::getDb()->getOne('select count(*) from oxgbentries'));
    }

    /**
     * Test save entry.
     *
     * @return null
     */
    public function testSaveEntry()
    {
        $this->getSession()->setVariable('usr', 'xxx');
        $this->getSession()->setVariable('gbSessionFormId', 'xxx');

        $this->setRequestParameter('rvw_txt', 'xxx');
        $this->setRequestParameter('gbFormId', 'xxx');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var GuestbookEntry|PHPUnit_Framework_MockObject_MockObject $oView */
        $oView = $this->getMock("guestbookEntry", array("canAcceptFormData"));
        $oView->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $this->assertEquals('guestbook', $oView->saveEntry());

        $this->assertEquals(1, oxDb::getDb()->getOne('select count(*) from oxgbentries'));
    }
}
