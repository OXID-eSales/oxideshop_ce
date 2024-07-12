<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

class LinksTest extends \PHPUnit\Framework\TestCase
{
    private $_oxLinks;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $this->_oxLinks = oxNew("oxlinks", $tableViewNameGenerator->getViewName('oxlinks'));
        $this->_oxLinks->setId('testlink');

        $this->_oxLinks->oxlinks__oxurl = new oxField('http://www.oxid-esales.com', oxField::T_RAW);
        $this->_oxLinks->Save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $oLink = oxNew("oxlinks", $tableViewNameGenerator->getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());
        $this->assertSame('http://www.oxid-esales.com', $oLink->oxlinks__oxurl->value);
    }

    /**
     * tests save function with special chars
     */
    public function testDescWithHtmlEntity()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $oLink = oxNew("oxlinks", $tableViewNameGenerator->getViewName('oxlinks'));
        $oLink->load($this->_oxLinks->getId());

        $oLink->oxlinks__oxurldesc = new oxField('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"äüßö', oxField::T_RAW);
        $this->_oxLinks->Save();
        $this->assertSame('Link&, &amp;, !@#$%^&*%$$&@\'.,;p"äüßö', $oLink->oxlinks__oxurldesc->value);
    }
}
