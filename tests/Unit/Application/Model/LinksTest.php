<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;

class LinksTest extends \OxidTestCase
{
    private $_oxLinks;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oxLinks = oxNew("oxlinks", getViewName('oxlinks'));
        $this->_oxLinks->setId('testlink');
        $this->_oxLinks->oxlinks__oxurl = new oxField('http://www.oxid-esales.com', oxField::T_RAW);
        $this->_oxLinks->Save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sDelete = "delete from oxlinks where oxid='" . $this->_oxLinks->getId() . "'";
        oxDb::getDb()->Execute($sDelete);
        parent::tearDown();
    }

    /**
     * tests save and load function
     */
    public function testLoad()
    {
        $oLink = oxNew("oxlinks", getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());
        $this->assertEquals('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * tests save function with special chars
     */
    public function testDescWithHtmlEntity()
    {
        $oLink = oxNew("oxlinks", getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());
        $oLink->oxlinks__oxurldesc = new oxField('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"äüßö', oxField::T_RAW);
        $this->_oxLinks->Save();
        $this->assertEquals('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"äüßö', $oLink->oxlinks__oxurldesc->value);
    }
}
