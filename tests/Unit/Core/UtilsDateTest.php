<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxField;
use \stdclass;
use \DateTime;
use \oxRegistry;

class UtilsDateTest extends \OxidTestCase
{
    public function testFormatDBDate()
    {
        $oUtilsDate = oxNew('oxUtilsDate');
        $oNullVar = null;

        $this->assertNull($oUtilsDate->formatDBDate($oNullVar));
        $this->assertNull($oUtilsDate->formatDBDate(false));
        $this->assertNotNull($oUtilsDate->formatDBDate(true));

        $aDates[] = array("14.11.2008", "2008-11-14", false);
        $aDates[] = array("2007-07-20 12:02:07", "2007-07-20 12:02:07", true);
        $aDates[] = array("2007-07-20", "2007-07-20", true);
        $aDates[] = array("-", "0000-00-00", false);
        $aDates[] = array("-", "0000-00-00 00:00:00", false);
        $aDates[] = array("0000-00-00 00:00:00", "-", false);
        $aDates[] = array("19.08.2007", "19.08.2007", false);
        $aDates[] = array("2007-08-20", "20.08.2007", true);
        $aDates[] = array("19.08.2007 12:02:07", "19.08.2007 12:02:07", false);
        $aDates[] = array("2007-08-19 12:02:07", "19.08.2007 12:02:07", true);
        $aDates[] = array("2007-08-19", "19.08.2007", true);
        $aDates[] = array("2007-08-19 12:02:07", "19.08.2007 12:02:07", true);
        $aDates[] = array("22.03.2003 10:04:09", "20030322100409", false);
        $aDates[] = array("2003-03-22 10:04:09", "20030322100409", true);
        $aDates[] = array("22.03.2003", "20030322", false);
        $aDates[] = array("2003-03-22", "20030322", true);
        $aDates[] = array(date("d.m.Y"), "simpleDateFormat", false);
        $aDates[] = array(date("Y-m-d"), "simpleDateFormat", true);

        foreach ($aDates as $aDate) {
            list($sResult, $sInput, $blForce) = $aDate;
            $this->assertEquals($sResult, $oUtilsDate->formatDBDate($sInput, $blForce));
        }
    }

    public function testGetTime()
    {
        $this->setConfigParam('iServerTimeShift', null); //explicitly set timezone to null
        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime(), time());
        for ($iTimeZone = -12; $iTimeZone < 15; $iTimeZone++) {
            $this->setConfigParam('iServerTimeShift', $iTimeZone);
            $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime(), (time() + ($this->getConfigParam('iServerTimeShift') * 3600)));
        }
    }

    public function testGetWeekNumber()
    {
        $sTimeStamp = '1186052540'; // from 2007-08-02 -> week nr = 31;

        $this->assertEquals(31, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(0, $sTimeStamp));
        $this->assertEquals(30, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(0, $sTimeStamp, '%U'));
        $this->assertEquals(31, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(0, $sTimeStamp, '%W'));

        $this->assertEquals(30, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(1, $sTimeStamp));

        $sCurTimeStamp = time();
        $iCurWeekNr = (int) strftime('%U', $sCurTimeStamp);
        $this->assertEquals($iCurWeekNr, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getWeekNumber(1));
    }

    /**
     *  Test german date to english format
     */
    public function testGerman2English()
    {
        $this->assertEquals('2008-05-25', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->german2English('25.05.2008'));
        $this->assertEquals('2008-05', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->german2English('05.2008'));
        $this->assertEquals('08-05-25', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->german2English('25.05.08'));
    }

    /**
     *  Test checking if date is empty
     */
    public function testIsEmptyDate()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('2008-05-08'));
        $this->assertFalse(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('25.05.2008'));
        $this->assertFalse(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('2008-06-18 00:00'));
        $this->assertFalse(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('0000/00/00 00:01'));
        $this->assertFalse(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('Some Text'));
        $this->assertTrue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate(''));
        $this->assertTrue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('0000-00-00'));
        $this->assertTrue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('0000/00/00'));
        $this->assertTrue(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->isEmptyDate('0000-00-00 00:00:00'));
    }

    /**
     * Testing date formatted
     */
    // few tests with insufficient input
    public function testConvertDBDateTimeDateNotFound()
    {
        $oObject = new oxField('xxx', oxField::T_RAW);

        $sReturn = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oObject, false, false);

        $this->assertEquals('xxx', $sReturn);
    }

    public function testConvertDBDateTimeTimeNotFound()
    {
        $oObject = new oxField('2007-08-01', oxField::T_RAW);

        $sReturn = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oObject, false, false);

        $this->assertEquals('2007-08-01', $sReturn);
    }

    // bunch of tests ...
    public function testConvertDBDateTime()
    {
        $sZeroTimeStandard = '0000-00-00 00:00:00';
        $sZeroTimeMySQL = '0000-00-00 00:00:00';
        $sZeroFormattedDate = '0000-00-00';

        $sDateTime = '2007-08-01 11:56:25';
        $sDateTimeStandard = '2007-08-01 11:56:25';
        $sDateTimeMySQL = '2007-08-01 11:56:25';
        $sDateFormattedDate = '2007-08-01';

        $sEURDateTime = '01.08.2007 11.56.25';

        $sUSADateTimeAM = '08/01/2007 11:56:25 AM';
        $sUSADateTimeAMExpected = '2007-08-01 11:56:25';

        $sUSADateTimePM = '08/01/2007 11:56:25 PM';
        $sUSADateTimePMStandard = '2007-08-01 23:56:25';
        $sUSADateTimePMMySQL = '2007-08-01 23:56:25';

        // standard
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroTimeStandard));

        // mySQL compatible
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroTimeMySQL, true));

        // format date
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest("", $sZeroFormattedDate, false, true));

        // ISO
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sDateTime, $sDateFormattedDate, false, true));

        // EUR
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sEURDateTime, $sDateFormattedDate, false, true));

        // USA pattern AM
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateTimeStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateTimeMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimeAM, $sDateFormattedDate, false, true));

        // USA pattern PM
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sUSADateTimePMStandard));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sUSADateTimePMMySQL, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sDateFormattedDate, true, true));
        $this->assertTrue($this->_ConvertDBDateTimeTest($sUSADateTimePM, $sDateFormattedDate, false, true));
    }

    /**
     * _ConvertDBDateTimeTest
     *
     * @param   string  datetime to be converted
     * @param   string  datetime expected after conversion
     * @param   bool    format as mysql compatible
     * @param   bool    format to date only
     * @param   bool    skip
     */
    protected function _ConvertDBDateTimeTest($sInput = "", $sExpected = "", $blMysql = false, $blFormatDate = false)
    {
        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject = new oxField($sInput, oxField::T_RAW);
            $oConvObject->fldmax_length = strlen($sInput);
            $oConvObject->fldtype = "datetime";
        }
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oConvObject, $blMysql, $blFormatDate);
        //echo "\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        if ($oConvObject->value == $sExpected) {
            return true;
        }

        return false;
    }

    /**
     * Note:    ConvertDBTimestamp() uses mktime() which is known to have issues with dates
     *          before 1970-01-01 00:00:00
     *          Before this date, all timestamps are computed in a cyclic interval of (2038-1970) in seconds
     *          and stored in a big int.
     *          so use caution with dates before the magic unix date!!
     */
    public function test_ConvertDBTimestamp()
    {
        $sDateTimeStamp = '20070801115625';
        $sDateTime = '2007-08-01 11:56:25';

        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTime, $sDateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTimeStamp, $sDateTime));

        $sDateTimeStamp = '20070801115625';
        $sEURDateTime = '01.08.2007 11.56.25';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sEURDateTime, $sDateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTimeStamp, $sDateTime));

        $sDateTimeStamp = '20070801115625';
        $sUSADateTime = '08/01/2007 11:56:25 AM';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sUSADateTime, $sDateTimeStamp, true));
        // input timestamp expect datetime
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTimeStamp, $sDateTime));

        $sDateTimeStamp = '20070801235625';
        $sUSADateTime = '08/01/2007 11:56:25 PM';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sUSADateTime, $sDateTimeStamp, true));
        // input timestamp expect datetime
        $sDateTime = '2007-08-01 23:56:25';
        $this->assertTrue($this->_ConvertDBTimestampTest($sDateTimeStamp, $sDateTime));

        $sZeroTimeStamp = '00000000000000';
        $sZeroDateTime = '0000-00-00 00:00:00';
        // input datetime expect timestamp
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroDateTime, $sZeroTimeStamp, true));
        // input timestamp expect datetime
        $sZeroTimeStamp = '19700101000000';
        $sZeroDateTime = '1970-01-01 00:00:00';
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));

        // 20070801-AS - timestamps works only for dates including 19011213205513
        $sZeroTimeStamp = '19111213205513';
        $sZeroDateTime = '1911-12-13 20:55:13';
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));

        // 20070801-AS - timestamps earlier than 19011213205513 return 1970-01-01 01:00:00
        $sZeroTimeStamp = '19711213205512';
        $sZeroDateTime = '1901-12-13 20:55:12';
        $this->assertFalse($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));
        // 20070801-AS - timestamps earlier than 19011213205513 return 1970-01-01 01:00:00 or differnt (depends on GMT + and - )
        $sZeroTimeStamp = '18710130105512';
        if (($iTimeStamp = mktime(10, 55, 12, 1, 30, 1871)) === false) {
            $iTimeStamp = 0;
        }
        $sZeroDateTime = date("Y-m-d H:i:s", $iTimeStamp);
        $this->assertTrue($this->_ConvertDBTimestampTest($sZeroTimeStamp, $sZeroDateTime));
    }

    /**
     * _ConvertDBTimestampTest
     *
     * @param   string  datetime/timestamp to be converted
     * @param   string  datetime/timestamp expected after conversion
     * @param   bool    if true convert to timestamp
     * @param   bool    skip
     */
    protected function _ConvertDBTimestampTest($sInput = "", $sExpected = "", $blToTimeStamp = false, $blSkip = false)
    {
        $myConfig = $this->getConfig();

        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject = new oxField($sInput, oxField::T_RAW);
        }

        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBTimestamp($oConvObject, $blToTimeStamp);
        if ($oConvObject->value == $sExpected) {
            return true;
        }

        return false;
    }

    public function testConvertDBDate()
    {
        $sDateTime = '2007-08-01 11:56:25';
        $sDate = '2007-08-01';
        $this->assertTrue($this->_ConvertDBDateTest($sDateTime, $sDate, false, false));
    }

    /**
     * Testing default time value setter
     */
    // bad input
    public function testSetDefaultFormatedValueBadInput()
    {
        $oObject = new oxField('xxx', oxField::T_RAW);
        $oObject->fldmax_length = 0;

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITsetDefaultFormatedValue($oObject, 'xxx', 'ISO', 'ISO', false);
        $this->assertEquals('xxx', $oObject->value);
        $this->assertEquals(0, $oObject->fldmax_length);
    }

    // only date
    public function testSetDefaultFormatedValueOnlyDate()
    {
        $oObject = new oxField('', oxField::T_RAW);
        $oObject->fldmax_length = 0;

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITsetDefaultFormatedValue($oObject, 'xxx', 'ISO', 'ISO', true);
        $this->assertEquals('0000-00-00', $oObject->value);
        $this->assertEquals(strlen('0000-00-00'), $oObject->fldmax_length);
    }

    // full date
    public function testSetDefaultFormatedValueFullDate()
    {
        $oObject = new oxField('', oxField::T_RAW);
        $oObject->fldmax_length = 0;

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITsetDefaultFormatedValue($oObject, '0000-00-00 00:00:00', 'ISO', 'ISO', false);
        $this->assertEquals('0000-00-00 00:00:00', $oObject->value);
        $this->assertEquals(strlen('0000-00-00 00:00:00'), $oObject->fldmax_length);
    }

    /**
     * _ConvertDBDateTest
     *
     * @param   string  date/timestamp to be converted
     * @param   string  date/timestamp expected after conversion
     * @param   bool    if true convert to timestamp
     * @param   bool    skip
     */
    protected function _ConvertDBDateTest($sInput = "", $sExpected = "", $blToTimeStamp = false)
    {
        $oConvObject = new oxField();
        if (!empty($sInput)) {
            $oConvObject = new oxField($sInput, oxField::T_RAW);
        }
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oConvObject, $blToTimeStamp);
        //echo "\nReturned: ->".$oConvObject->value."<-\nExpected: ->".$sExpected.'<-';
        if ($oConvObject->value == $sExpected) {
            return true;
        }

        return false;
    }

    /**
     * Testing default date time value setter
     */
    public function testSetDefaultDateTimeValue()
    {
        $oObject = new stdclass();

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITsetDefaultDateTimeValue($oObject, "ISO", "ISO", false);

        $this->assertEquals("0000-00-00 00:00:00", $oObject->value);
        $this->assertEquals(strlen("0000-00-00 00:00:00"), $oObject->fldmax_length);
    }

    /**
     * Testing date formatter
     */
    public function testSetDate()
    {
        $oObject = new stdclass();

        $aDateMatches = array(05, 14, 1981,);
        $aDFields = array(0, 1, 2);

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITsetDate($oObject, "Y-m-d", $aDFields, $aDateMatches);

        $this->assertEquals("1981-05-14", $oObject->value);
        $this->assertEquals(strlen("1981-05-14"), $oObject->fldmax_length);
    }

    /**
     * Testing full date formatter
     */
    public function testFormatCorrectTimeValue()
    {
        $oObject = new stdclass();

        $aDateMatches = array(05, 14, 1981,);
        $aDFields = array(0, 1, 2);

        $aTimeMatches = array(12, 12, 12);
        $aTFields = array(0, 1, 2);

        $oxUtilsDate = $this->getProxyClass('oxUtilsDate');
        $oxUtilsDate->UNITformatCorrectTimeValue($oObject, "Y-m-d", "H:i:s", $aDateMatches, $aTimeMatches, $aTFields, $aDFields);

        $this->assertEquals("1981-05-14 12:12:12", $oObject->value);
        $this->assertEquals(strlen("1981-05-14 12:12:12"), $oObject->fldmax_length);
    }

    /**
     *  Test static time value
     */
    public function testGetTime_static()
    {
        $this->setTime(157);
        $this->assertEquals(157, $this->getTime());
        $this->assertEquals(157, \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
    }

    public function testFormTimeNoTimeShift()
    {
        $this->setConfigParam('iServerTimeShift', null);

        $oDateTime = new DateTime('tomorrow');
        $iExpectedTimeStamp = $oDateTime->getTimestamp();

        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formTime('tomorrow'), $iExpectedTimeStamp);
    }

    public function testFormTimeNoTimeShiftHourSet()
    {
        $this->setConfigParam('iServerTimeShift', null);

        $oDateTime = new DateTime('tomorrow');
        $oDateTime->setTime(17, 10, 15);
        $iExpectedTimeStamp = $oDateTime->getTimestamp();

        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formTime('tomorrow', '17:10:15'), $iExpectedTimeStamp);
    }

    public function testFormTimeWithTimeShift()
    {
        $iTimeShiftHours = 2;
        $iTimeShiftSeconds = $iTimeShiftHours * 3600;
        $this->setConfigParam('iServerTimeShift', $iTimeShiftHours);

        $oDateTime = new DateTime('tomorrow');
        $iExpectedTimeStamp = $oDateTime->getTimestamp() + $iTimeShiftSeconds;

        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formTime('tomorrow'), $iExpectedTimeStamp);
    }

    public function testFormTimeWithTimeShiftHourSet()
    {
        $iTimeShiftHours = 2;
        $iTimeShiftSeconds = $iTimeShiftHours * 3600;
        $this->setConfigParam('iServerTimeShift', $iTimeShiftHours);

        $oDateTime = new DateTime('tomorrow');
        $oDateTime->setTime(17, 10, 15);
        $iExpectedTimeStamp = $oDateTime->getTimestamp() + $iTimeShiftSeconds;

        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->formTime('tomorrow', '17:10:15'), $iExpectedTimeStamp);
    }

    public function providerShiftServerTime()
    {
        return array(
            array(2),
            array(0),
            array(null),
        );
    }

    /**
     * @dataProvider providerShiftServerTime
     *
     * @param int $iTimeShiftHours
     */
    public function testShiftServerTime($iTimeShiftHours)
    {
        $iTimeShiftSeconds = (int) $iTimeShiftHours * 3600;
        $this->setConfigParam('iServerTimeShift', $iTimeShiftHours);

        $iCurrentTime = time();
        $iExpectedTimeStamp = $iCurrentTime + $iTimeShiftSeconds;

        /** @var oxUtilsDate $oUtilsDate */
        $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
        $this->assertSame($iExpectedTimeStamp, $oUtilsDate->shiftServerTime($iCurrentTime));
    }
}
