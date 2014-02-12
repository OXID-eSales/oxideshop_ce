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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class dealing with regular string handling
 */
class oxStrRegular
{
    /**
     * The character encoding.
     *
     * @var string
     */
    protected $_sEncoding = 'ISO8859-15';

    /**
     * Language specific characters (currently german; storen in octal form)
     *
     * @var array
     */
    protected $_aUmls = array( "\344", "\366", "\374", "\304", "\326", "\334", "\337" );

    /**
     * oxUtilsString::$_aUmls equivalent in entities form
     * @var array
     */
    protected $_aUmlEntities = array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;' );

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * PHP strlen() function wrapper
     *
     * @param string $sStr strint to mesure its length
     *
     * @return int
     */
    public function strlen($sStr)
    {
        return strlen($sStr);
    }

    /**
     * PHP substr() function wrapper
     *
     * @param string $sStr    value to truncate
     * @param int    $iStart  start position
     * @param int    $iLength length
     *
     * @return string
     */
    public function substr($sStr, $iStart, $iLength = null)
    {
        if (is_null($iLength)) {
            return substr($sStr, $iStart);
        } else {
            return substr($sStr, $iStart, $iLength);
        }
    }

    /**
     * PHP strpos() function wrapper
     *
     * @param string $sHaystack value to search in
     * @param string $sNeedle   value to search for
     * @param int    $iOffset   initial search position
     *
     * @return string
     */
    public function strpos($sHaystack, $sNeedle, $iOffset = null)
    {
        $iPos = false;
        if ( $sHaystack && $sNeedle ) {
            if ( is_null( $iOffset ) ) {
                $iPos = strpos( $sHaystack, $sNeedle );
            } else {
                $iPos = strpos( $sHaystack, $sNeedle, $iOffset );
            }
        }
        return $iPos;
    }

    /**
     * PHP strstr() function wrapper
     *
     * @param string $sHaystack string searching in
     * @param string $sNeedle   string to search
     *
     * @return mixed
     */
    public function strstr($sHaystack, $sNeedle)
    {
        return strstr($sHaystack, $sNeedle);
    }

    /**
     * PHP multibute compliant strtolower() function wrapper
     *
     * @param string $sString string being lowercased
     *
     * @return string
     */
    public function strtolower($sString)
    {
        return strtolower($sString);
    }

    /**
     * PHP strtolower() function wrapper
     *
     * @param string $sString string being lowercased
     *
     * @return string
     */
    public function strtoupper($sString)
    {
        return strtoupper($sString);
    }

    /**
     * PHP htmlspecialchars() function wrapper
     *
     * @param string $sString string being converted
     *
     * @return string
     */
    public function htmlspecialchars($sString)
    {
        return htmlspecialchars( $sString, ENT_QUOTES, $this->_sEncoding );
    }

    /**
     * PHP htmlentities() function wrapper
     *
     * @param string $sString string being converted
     *
     * @return string
     */
    public function htmlentities($sString)
    {
        return htmlentities( $sString, ENT_QUOTES, $this->_sEncoding );
    }

    /**
     * PHP html_entity_decode() function wrapper
     *
     * @param string $sString string being converted
     *
     * @return string
     */
    public function html_entity_decode($sString)
    {
        return html_entity_decode( $sString, ENT_QUOTES, $this->_sEncoding );
    }

    /**
     * PHP preg_split() function wrapper
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sString  input string
     * @param int    $iLimit   (optional) only substrings up to limit are returned
     * @param int    $iFlag    flags
     *
     * @return string
     */
    public function preg_split($sPattern, $sString, $iLimit = -1, $iFlag = 0)
    {
        return preg_split( $sPattern, $sString, $iLimit, $iFlag );
    }

    /**
     * PHP preg_replace() function wrapper
     *
     * @param mixed  $sPattern pattern to search for, as a string
     * @param mixed  $sString  string to replace
     * @param string $sSubject strings to search and replace
     * @param int    $iLimit   maximum possible replacements
     * @param int    $iCount   number of replacements done
     *
     * @return string
     */
    public function preg_replace($sPattern, $sString, $sSubject, $iLimit = -1, $iCount = null)
    {
        return preg_replace( $sPattern, $sString, $sSubject, $iLimit, $iCount);
    }

    /**
     * PHP preg_match() function wrapper
     *
     * @param string $sPattern  pattern to search for, as a string
     * @param string $sSubject  input string
     * @param array  &$aMatches is filled with the results of search
     * @param int    $iFlags    flags
     * @param int    $iOffset   place from which to start the search
     *
     * @return string
     */
    public function preg_match($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null)
    {
        return preg_match( $sPattern, $sSubject, $aMatches, $iFlags, $iOffset);
    }

    /**
     * PHP preg_match_all() function wrapper
     *
     * @param string $sPattern  pattern to search for, as a string
     * @param string $sSubject  input string
     * @param array  &$aMatches is filled with the results of search
     * @param int    $iFlags    flags
     * @param int    $iOffset   place from which to start the search
     *
     * @return string
     */
    public function preg_match_all($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null)
    {
        return preg_match_all( $sPattern, $sSubject, $aMatches, $iFlags, $iOffset);
    }

    /**
     * PHP ucfirst() function wrapper
     *
     * @param string $sSubject input string
     *
     * @return string
     */
    public function ucfirst($sSubject)
    {
        $sString = $this->strtoupper($this->substr($sSubject, 0, 1));
        return $sString . $this->substr($sSubject, 1);
    }

    /**
     * PHP wordwrap() function wrapper
     *
     * @param string $sString input string
     * @param int    $iLength column width
     * @param string $sBreak  line is broken using the optional break parameter
     * @param bool   $blCut   string is always wrapped at the specified width
     *
     * @return string
     */
    public function wordwrap($sString, $iLength = 75, $sBreak = "\n", $blCut = null )
    {
        return wordwrap($sString, $iLength, $sBreak, $blCut);
    }

    /**
     * Recodes and returns passed input:
     *     if $blToHtmlEntities == true  ä -> &auml;
     *     if $blToHtmlEntities == false &auml; -> ä
     *
     * @param string $sInput           text to recode
     * @param bool   $blToHtmlEntities recode direction
     * @param array  $aUmls            language specific characters
     * @param array  $aUmlEntities     language specific characters equivalents in entities form
     *
     * @return string
     */
    public function recodeEntities( $sInput, $blToHtmlEntities = false, $aUmls = array(), $aUmlEntities = array() )
    {
        $aUmls = ( count( $aUmls ) > 0 ) ? array_merge( $this->_aUmls, $aUmls) : $this->_aUmls;
        $aUmlEntities = ( count( $aUmlEntities ) > 0 ) ? array_merge( $this->_aUmlEntities, $aUmlEntities) : $this->_aUmlEntities;
        return $blToHtmlEntities ? str_replace( $aUmls, $aUmlEntities, $sInput ) : str_replace( $aUmlEntities, $aUmls, $sInput );
    }

    /**
     * Checks if string has special chars
     *
     * @param string $sStr string to search in
     *
     * @return bool
     */
    public function hasSpecialChars( $sStr )
    {
        return $this->preg_match( "/(".implode( "|", $this->_aUmls  )."|(&amp;))/", $sStr );
    }

    /**
     * Replaces special characters with passed char.
     * Special chars are: \n \r \t x95 xa0 ;
     *
     * @param string $sStr      string to cleanup
     * @param object $sCleanChr which character should be used as a replacement (default is empty space)
     *
     * @return string
     */
    public function cleanStr( $sStr, $sCleanChr = ' ')
    {
        return $this->preg_replace( "/\n|\r|\t|\x95|\xa0|;/", $sCleanChr, $sStr );
    }

    /**
     * wrapper for json encode, which does not work with non utf8 characters
     *
     * @param mixed $data data to encode
     *
     * @return string
     */
    public function jsonEncode($data)
    {
        if (is_array($data)) {
            $ret = "";
            $blWasOne = false;
            $blNumerical = true;
            reset($data);
            while ($blNumerical && (list($key) = each($data))) {
                $blNumerical = !is_string($key);
            }
            if ($blNumerical) {
                return '['.  implode(',', array_map(array($this, 'jsonEncode'), $data)).']';
            } else {
                foreach ($data as $key => $val) {
                    if ($blWasOne) {
                        $ret .= ',';
                    } else {
                        $blWasOne = true;
                    }
                    $ret .= '"'.addslashes($key).'":'. $this->jsonEncode($val);
                }
                return "{".$ret."}";
            }
        } else {
            return '"'.addcslashes((string)$data, "\r\n\t\"\\").'"';
        }
    }

    /**
     * PHP strip_tags() function wrapper.
     *
     * @param string $sString        the input string
     * @param string $sAllowableTags an optional parameter to specify tags which should not be stripped
     *
     * @return string
     */
    public function strip_tags( $sString, $sAllowableTags = '' )
    {
        if ( stripos( $sAllowableTags, '<style>' ) === false ) {
            // strip style tags with definitions within
            $sString = $this->preg_replace( "'<style[^>]*>.*</style>'siU", '', $sString );
        }
        return strip_tags( $sString, $sAllowableTags );
    }

    /**
     * Compares two strings. Case sensitive.
     * For use in sorting with reverse order
     *
     * @param string $sStr1 String to compare
     * @param string $sStr2 String to compare
     *
     * @return int > 0 if str1 is less than str2; < 0 if str1 is greater than str2, and 0 if they are equal.
     */
    public function strrcmp( $sStr1, $sStr2 )
    {
        return -strcmp( $sStr1, $sStr2 );
    }
}
