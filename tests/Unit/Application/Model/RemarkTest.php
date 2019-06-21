<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxRegistry;

class RemarkTest extends \OxidTestCase
{
    private $_oRemark = null;

    protected $_iNow = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_iNow = time();
        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->UNITSetTime($this->_iNow);

        $this->_oRemark = oxNew('oxremark');
        $this->_oRemark->oxremark__oxtext = new oxField('Test', oxField::T_RAW);
        $this->_oRemark->save();
        $this->_oRemark->load($this->_oRemark->getId());
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oRemark->delete();

        parent::tearDown();
    }

    public function testLoad()
    {
        $oRemark = oxNew('oxremark');
        $oRemark->load($this->_oRemark->oxremark__oxid->value);

        $sSendDate = 'd.m.Y H:i:s';
        if (oxRegistry::getLang()->getBaseLanguage() == 1) {
            $sSendDate = 'Y-m-d H:i:s';
        }

        $this->assertEquals(date($sSendDate, $this->_iNow), $oRemark->oxremark__oxcreate->value);
    }

    public function testUpdate()
    {
        $oRemark = oxNew('oxremark');
        $oRemark->load($this->_oRemark->getId());

        $oRemark->oxremark__oxtext = new oxField("Test_remark", oxField::T_RAW);
        $oRemark->oxremark__oxparentid = new oxField("oxdefaultadmin", oxField::T_RAW);
        $oRemark->save();

        $this->assertEquals($oRemark->oxremark__oxtext->value, 'Test_remark');
        $this->assertEquals($oRemark->oxremark__oxcreate->value, $this->_oRemark->oxremark__oxcreate->value);
    }

    public function testInsert()
    {
        $iNow = time();

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->UNITSetTime($iNow);

        $oRemark = oxNew('oxremark');
        $oRemark->load($this->_oRemark->oxremark__oxid->value);
        $oRemark->delete();

        $oRemark = oxNew('oxremark');
        $oRemark->setId($this->_oRemark->oxremark__oxid->value);
        $oRemark->save();

        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $oRemark->oxremark__oxcreate->value);
    }
}
