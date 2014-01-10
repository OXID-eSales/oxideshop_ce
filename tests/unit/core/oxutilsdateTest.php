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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxUtilsDateTest extends OxidTestCase
{

    public function testFormatDBDate()
    {
        $oUtilsDate = new oxUtilsDate();
        $oNullVar = null;

        $this->assertNull($oUtilsDate->formatDBDate($oNullVar));
        $this->assertNull($oUtilsDate->formatDBDate(false));
        $this->assertNotNull($oUtilsDate->formatDBDate(true));

        $aDates[] = array( "14.11.2008", "2008-11-14", false );
        $aDates[] = array( "2007-07-20 12:02:07", "2007-07-20 12:02:07", true );
        $aDates[] = array( "2007-07-20", "2007-07-20", true );
        $aDates[] = array( "-", "0000-00-00", false );
        $aDates[] = array( "-", "0000-00-00 00:00:00", false );
        $aDates[] = array( "0000-00-00 00:00:00", "-", false );
        $aDates[] = array( "19.08.2007", "19.08.2007", false );
        $aDates[] = array( "2007-08-20", "20.08.2007", true );
        $aDates[] = array( "19.08.2007 12:02:07", "19.08.2007 12:02:07", false );
        $aDates[] = array( "2007-08-19 12:02:07", "19.08.2007 12:02:07", true );
        $aDates[] = array( "2007-08-19", "19.08.2007", true );
        $aDates[] = array( "2007-08-19 12:02:07", "19.08.2007 12:02:07", true );
        $aDates[] = array( "22.03.2003 10:04:09", "20030322100409", false );
        $aDates[] = array( "2003-03-22 10:04:09", "20030322100409", true );
        $aDates[] = array( "22.03.2003", "20030322", false );
        $aDates[] = array( "2003-03-22", "20030322", true );
        $aDates[] = array( date( "d.m.Y" ), "simpleDateFormat", false );
        $aDates[] = array( date( "Y-m-d" ), "simpleDateFormat", true );

        foreach ( $aDates as $aDate) {
            list( $sResult, $sInput, $blForce ) = $aDate;
            $this->assertEquals( $sResult, $oUtilsDate->formatDBDate( $sInput, $blForce ) );
        }
    }

    public function testGetTime()
    {
        $myConfig = oxConfig::getInstance();
        modConfig::getInstance()->setConfigParam('iServerTimeShift', null); //explicitly set timezone to null
        $this->assertEquals(oxUtilsDate::getInstance()->getTime(), time());
        for ($iTimeZone = -12; $iTimeZone < 15; $iTimeZone++) {
            modConfig::getInstance()->setConfigParam('iServerTimeShift', $iTimeZone);
            $this->assertEquals(oxUtilsDate::getInstance()->getTime(), (time() + (modConfig::getInstance()->getConfigParam('iServerTimeShift') * 3600)));
        }
    }

    public function testGetWeekNumber()
    {

        $sTimeStamp = '1186052540'; // from 2007-08-02 -> week nr = 31;

        $this->assertEquals(31, oxUtilsDate::getInstance()->getWeekNumber(0, $sTimeStamp));
        $this->assertEquals(30, oxUtilsDate::getInstance()->getWeekNumber(0, $sTimeStamp, '%U'));
        $this->assertEquals(31, oxUtilsDate::getInstance()->getWeekNumber(0, $sTimeStamp, '%W'));

        $this->assertEquals(30, oxUtilsDate::getInstance()->getWeekNumber(1, $sTimeStamp));

        $sCurTimeStamp = time();
        $iCurWeekNr = (int) strftime( '%U', $sCurTimeStamp);
        $this->assertEquals($iCurWeekNr, oxUtilsDate::getInstance()->getWeekNumber(1));
    }

    /**
     *  Test german date to english format
     */
    public function testGerman2English()
    {
        $this->assertEquals( '2008-05-25', oxUtilsDate::getInstance()->german2English( '25.05.2008' ) );
        $this->assertEquals( '2008-05', oxUtilsDate::getInstance()->german2English( '05.2008' ) );
        $this->assertEquals( '08-05-25', oxUtilsDate::getInstance()->german2English( '25.05.08' ) );
    }

    /**
     *  Test checking if date is empty
     */
    public function testIsEmptyDate()
    {
        $this->assertFalse( oxUtilsDate::getInstance()->isEmptyDate( '2008-05-08' ) );
        $this->assertFalse( oxUtilsDate::getInstance()->isEmptyDate( '25.05.2008' ) );
        $this->assertFalse( oxUtilsDate::getInstance()->isEmptyDate( '2008-06-18 00:00' ) );
        $this->assertFalse( oxUtilsDate::getInstance()->isEmptyDate( '0000/00/00 00:01' ) );
        $this->assertFalse( oxUtilsDate::getInstance()->isEmptyDate( 'Some Text' ) );
        $this->assertTrue( oxUtilsDate::getInstance()->isEmptyDate( '' ) );
        $this->assertTrue( oxUtilsDate::getInstance()->isEmptyDate( '0000-00-00' ) );
        $this->assertTrue( oxUtilsDate::getInstance()->isEmptyDate( '0000/00/00' ) );
        $this->assertTrue( oxUtilsDate::getInstance()->isEmptyDate( '0000-00-00 00:00:00' ) );
    }

}
