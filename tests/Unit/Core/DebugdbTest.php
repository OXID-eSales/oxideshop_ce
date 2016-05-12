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

use modDB;
use \oxDb;
use OxidEsales\Eshop\Core\ShopIdCalculator;

/**
 * testing oxattributelist class.
 */
class DebugdbTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function setUp()
    {
        oxDb::getDb()->execute('drop table IF EXISTS adodb_logsql');
        $sCreateLogTable = "CREATE TABLE adodb_logsql (
		  created datetime NOT NULL,
		  sql0 varchar(250) NOT NULL,
		  sql1 text NOT NULL,
		  params text NOT NULL,
		  tracer text NOT NULL,
		  timer decimal(16,6) NOT NULL
		)";

        $sInsert = "

        INSERT INTO `adodb_logsql` (`created`, `sql0`, `sql1`, `params`, `tracer`, `timer`) VALUES
('2010-03-23 19:13:49', '130.-1711623172', 'select oxvarname, oxvartype, DECODE( oxvarvalue, ''fq45QS09_fqyx09239QQ'') as oxvarvalue from oxconfig where oxshopid = ''".ShopIdCalculator::BASE_SHOP_ID."''', '', '', 1.000000),
('2010-03-23 19:13:49', '206.-204246693', 'select oxfixed, oxseourl, oxexpired, oxtype from oxseo where oxtype = ''oxmanufacturer''\n               and oxobjectid = ''root'' and oxshopid = ''".ShopIdCalculator::BASE_SHOP_ID."'' and oxlang = ''1'' order by oxparams = '''' desc limit 1', '', '', 1.000000),
('2010-03-23 19:13:49', '234.-62183175', 'select oxfixed, oxseourl, oxexpired, oxtype from oxseo where oxtype = ''oxmanufacturer''\n               and oxobjectid = ''ee4948794e28d488cf1c8101e716a3f4'' and oxshopid = ''".ShopIdCalculator::BASE_SHOP_ID."'' and oxlang = ''1'' order by oxparams = '''' desc limit 1', '', '', 1.000000),
('2010-03-23 19:13:50', '1486.-19846082', 'select oxarticles.oxid, oxarticles.oxparentid, oxarticles.oxvarcount, oxarticles.oxvarstock, oxarticles.oxstock, oxarticles.oxstockflag, oxarticles.oxprice, oxarticles.oxvat, oxarticles.oxskipdiscounts, oxarticles.oxunitquantity, oxarticles.oxshopid, oxarticles.oxunitname, oxarticles.oxtitle_1 as oxtitle, oxarticles.oxvarselect_1 as oxvarselect, oxarticles.oxicon, oxarticles.oxthumb, oxarticles.oxartnum, oxarticles.oxtprice, oxarticles.oxpic1, oxarticles.oxshortdesc_1 as oxshortdesc, oxarticles.oxvarname_1 as oxvarname from oxactions2article\n                              left join oxarticles on oxarticles.oxid = oxactions2article.oxartid\n                              left join oxactions on oxactions.oxid = oxactions2article.oxactionid\n                              where oxactions2article.oxshopid = ''".ShopIdCalculator::BASE_SHOP_ID."'' and oxactions2article.oxactionid = ''oxbargain'' and (   oxactions.oxactive = 1  or  ( oxactions.oxactivefrom < ''2010-03-23 19:13:50'' and oxactions.oxactiveto > ''2010-03-23 19:13:50'' ) ) \n                              and oxarticles.oxid is not null and (  oxarticles.oxactive = 1   and ( oxarticles.oxstockflag != 2 or ( oxarticles.oxstock + oxarticles.oxvarstock ) > 0  )  and IF( oxarticles.oxvarcount = 0, 1, ( select 1 from oxarticles as art where art.oxparentid=oxarticles.oxid and ( art.oxactive = 1  ) and ( art.oxstockflag != 2 or art.oxstock > 0 ) limit 1 ) )  ) \n                              order by oxactions2article.oxsort', '', '', 1.000000);

        ";

        oxDb::getDb()->Execute($sCreateLogTable);
        oxDb::getDb()->Execute($sInsert);

        parent::setUp();
    }

    /**
     * Setups the fixture
     *
     * @return null
     */
    public function tearDown()
    {
        //oxDb::getDb()->execute('drop table adodb_logsql');
        parent::tearDown();
    }

    public function testSkipWhiteSpace()
    {
        $this->markTestSkippedUntil('2016-06-29', 'We will remove this whole mysql logging in ESDEV-3511.');

        $oSubj = $this->getProxyClass("oxdebugdb");
        $sTestIn = "Test Val \ntest\tval\r ";
        $sExpOut = "TestValtestval";
        $sOut = $oSubj->UNITskipWhiteSpace($sTestIn);
        $this->assertEquals($sExpOut, $sOut);
    }

    /**
     * oxDebugDb class is not used in normal eshop execution,
     * thus we check only getWarning() method results
     */
    public function testGetWarnings()
    {
        $this->markTestSkippedUntil('2016-06-29', 'We will remove this whole mysql logging in ESDEV-3511.');
        
        modDB::$unitMOD = false;
        $oSubj = oxNew('oxDebugDb');
        $aOut = $oSubj->getWarnings();
        $this->assertEquals(4, count($aOut));
        $this->assertEquals(1, $aOut[0]['time']);
        $this->assertEquals('MESS', $aOut[0]['check']);
        $this->assertEquals('MESS', $aOut[1]['check']);
        $this->assertEquals('MESS_ALL', $aOut[2]['check']);
        $this->assertEquals('MESS_ALL', $aOut[3]['check']);
    }
}
