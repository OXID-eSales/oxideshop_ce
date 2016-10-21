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
namespace Unit\Core;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Testing myvoucherserie class
 */
class VoucherserieExcludeTest extends \OxidTestCase
{

    /**
     * Setting up environment
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_insertData();
    }

    protected function _insertData()
    {
        $sShopIdFields = "`OXSHOPID`";
        $sShopIdValues = ShopIdCalculator::BASE_SHOP_ID;

        $sInsertSeries = "
        INSERT INTO `oxvoucherseries`
        (`OXID`, $sShopIdFields, `OXSERIENR`, `OXSERIEDESCRIPTION`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXALLOWSAMESERIES`, `OXALLOWOTHERSERIES`, `OXALLOWUSEANOTHER`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`)
        VALUES
        ('test_s1',$sShopIdValues,'s1','regular   ','20','absolute','0000-00-00 00:00:00','0000-00-00 00:00:00',0,0,0,'0',0);";

        $sInsertVouchers = "
        INSERT INTO `oxvouchers`
        (`OXVOUCHERSERIEID`,`OXID`, `OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXDISCOUNT`)
        VALUES
        ('test_s1','test_111','0000-00-00','','',0,'111',NULL);";

        $sInsertReleations = "
        INSERT INTO `oxobject2discount`
        (`OXID`, `OXDISCOUNTID`, `OXOBJECTID`, `OXTYPE`)
        VALUES
        ('test_r1','test_s1','1771','oxarticles');";

        $this->addToDatabase($sInsertSeries, 'oxvoucherseries');
        $this->addToDatabase($sInsertVouchers, 'oxvouchers');
        $this->addToDatabase($sInsertReleations, 'oxobject2discount');
    }

    public function testDelete()
    {
        $oSerie = oxNew('oxvoucherserie');
        $oSerie->load('test_s1');

        $oSerie->delete();

        $iCountSeries = oxDb::getDb()->getOne("SELECT count(*) FROM `oxvoucherseries`   WHERE `OXID`= 'test_s1';");
        $iCountVouchers = oxDb::getDb()->getOne("SELECT count(*) FROM `oxvouchers`        WHERE `OXID`= 'test_111';");
        $iCountRelations = oxDb::getDb()->getOne("SELECT count(*) FROM `oxobject2discount` WHERE `OXID`= 'test_r1';");

        $this->assertEquals(0, $iCountSeries);
        $this->assertEquals(0, $iCountVouchers);
        $this->assertEquals(0, $iCountRelations);
    }

}

?>
