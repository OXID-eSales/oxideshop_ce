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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Date manipulation utility class
 */
class oxUtilsDate extends oxSuperCfg
{
    /**
     * oxUtils class instance.
     *
     * @var oxutils* instance
     */
    private static $_instance = null;

    /**
     * Returns object instance
     *
     * @return oxUtilsDate
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            self::$_instance = modInstances::getMod( __CLASS__ );
        }

        if ( !self::$_instance instanceof oxUtilsDate ) {
            self::$_instance = oxNew( 'oxUtilsDate' );

            if ( defined( 'OXID_PHP_UNIT' ) ) {
                modInstances::addMod( __CLASS__, self::$_instance);
            }
        }
        return self::$_instance;
    }

    /**
     * Reformats date to user defined format.
     *
     * @param string $sDBDateIn         Date to reformat
     * @param bool   $blForceEnglishRet Force to return primary value(default false)
     *
     * @return string
     */
    public function formatDBDate( $sDBDateIn, $blForceEnglishRet = false )
    {
        // convert english format to output format
        if ( !$sDBDateIn ) {
            return null;
        }

        $oStr = getStr();
        if ( $blForceEnglishRet && $oStr->strstr( $sDBDateIn, '-' ) ) {
            return $sDBDateIn;
        }

        if ( $this->isEmptyDate( $sDBDateIn ) && $sDBDateIn != '-' ) {
            return '-';
        } elseif ( $sDBDateIn == '-' ) {
            return '0000-00-00 00:00:00';
        }

        // is it a timestamp ?
        if ( is_numeric( $sDBDateIn ) ) {
            // db timestamp : 20030322100409
            $sNew  = substr( $sDBDateIn, 0, 4 ).'-'.substr( $sDBDateIn, 4, 2 ).'-'.substr( $sDBDateIn, 6, 2 ).' ';
            // check if it is a timestamp or wrong data: 20030322
            if ( strlen($sDBDateIn) > 8 ) {
                $sNew .= substr( $sDBDateIn, 8, 2 ).':'.substr( $sDBDateIn, 10, 2 ).':'.substr( $sDBDateIn, 12, 2 );
            }
            // convert it to english format
            $sDBDateIn = $sNew;
        }

        // remove time as it is same in english as in german
        $aData = explode( ' ', trim( $sDBDateIn ) );

        // preparing time array
        $sTime = ( isset( $aData[1] ) && $oStr->strstr( $aData[1], ':' ) )?$aData[1]:'';
        $aTime = $sTime?explode( ':', $sTime ):array( 0, 0, 0 );

        // preparind date array
        $sDate = isset( $aData[0] )?$aData[0]:'';
        $aDate = preg_split( '/[\/.-]/', $sDate );

        // choosing format..
        if ( $sTime ) {
            $sFormat = $blForceEnglishRet ? 'Y-m-d H:i:s' : oxLang::getInstance()->translateString( 'fullDateFormat' );
        } else {
            $sFormat = $blForceEnglishRet ? 'Y-m-d' : oxLang::getInstance()->translateString( 'simpleDateFormat' );
        }

        if ( count( $aDate ) != 3 ) {
            return date( $sFormat );
        } else {
            return $this->_processDate( $aTime, $aDate, $oStr->strstr( $sDate, '.' ), $sFormat );
        }
    }

    /**
     * Returns time according shop timezone configuration. Configures in
     * Admin -> Main menu -> Core Settings -> General
     *
     * @return int current (modified according timezone) time
     */
    public function getTime()
    {
        $iServerTimeShift = $this->getConfig()->getConfigParam( 'iServerTimeShift' );
        if ( !$iServerTimeShift ) {
            return time();
        }

        return ( time() + ( (int) $iServerTimeShift * 3600 ) );
    }

    /**
     * Returns number of the week according to numeration standards (configurable in admin):
     * %U - week number, starting with the first Sunday as the first day of the first week;
     * %W - week number, starting with the first Monday as the first day of the first week.
     *
     * @param int    $iFirstWeekDay if set formats with %U, otherwise with %W ($myConfig->getConfigParam( 'iFirstWeekDay' ))
     * @param string $sTimestamp    timestamp, default is null (returns current week number);
     * @param string $sFormat       calculation format ( "%U" or "%w"), default is null (returns "%W" or defined in admin ).
     *
     * @return int
     */
    public function getWeekNumber( $iFirstWeekDay,  $sTimestamp = null, $sFormat = null )
    {
        if ( $sTimestamp == null ) {
            $sTimestamp = time();
        }
        if ( $sFormat == null ) {
            $sFormat = '%W';
            if ( $iFirstWeekDay ) {
                $sFormat = '%U';
            }
        }
        return (int) strftime( $sFormat, $sTimestamp );
    }

    /**
     * Reformats and returns German date string to English.
     *
     * @param string $sDate German format date string
     *
     * @return string
     */
    public function german2English( $sDate )
    {
        $aDate = explode( ".", $sDate);

        if ( isset( $aDate ) && count( $aDate) > 1) {
            if ( count( $aDate) == 2) {
                $sDate = $aDate[1]."-".$aDate[0];
            } else {
                $sDate = $aDate[2]."-".$aDate[1]."-".$aDate[0];
            }
        }

        return $sDate;
    }

    /**
     * Processes amd formats date / time.
     *
     * @param string $aTime    splitted time ( array( H, m, s ) )
     * @param array  $aDate    splitted date ( array( Y, m, d ) )
     * @param bool   $blGerman true if incoming string is in German format (dotted)
     * @param string $sFormat  date format to produce
     *
     * @return string formatted string
     */
    protected function _processDate( $aTime, $aDate, $blGerman, $sFormat )
    {
        if ( $blGerman ) {
            return date( $sFormat, mktime( $aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2] ) );
        } else {
            return date( $sFormat, mktime( $aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[2], $aDate[0] ) );
        }
    }

    /**
     * Checks if date string is empty date field. Empty string or string with
     * all date values equal to 0 is treated as empty.
     *
     * @param array $sDate date or date time string
     *
     * @return bool
     */
    public function isEmptyDate( $sDate )
    {
        $blIsEmpty = true;

        if ( !empty( $sDate ) ) {
            $sDate = preg_replace("/[^0-9a-z]/i", "", $sDate);
            if ( is_numeric( $sDate ) && $sDate == 0 ) {
                $blIsEmpty = true;
            } else {
                $blIsEmpty = false;
            }
        }

        return $blIsEmpty;
    }

}

