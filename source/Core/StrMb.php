<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class dealing with multibyte strings
 */
class StrMb
{
    /**
     * The character encoding.
     *
     * @var string
     */
    protected $_sEncoding = 'UTF-8';

    /**
     * Language specific characters (currently german; storen in octal form)
     *
     * @var array
     */
    protected $_aUmls = ["\xc3\xa4", "\xc3\xb6", "\xc3\xbc", "\xC3\x84", "\xC3\x96", "\xC3\x9C", "\xC3\x9F"];

    /**
     * oxUtilsString::$_aUmls equivalent in entities form
     *
     * @var array
     */
    protected $_aUmlEntities = ['&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;'];

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     */
    public function __construct()
    {
    }

    /**
     * PHP  multi byte compliant strlen() function wrapper
     *
     * @param string $sStr string to measure its length
     *
     * @return int
     */
    public function strlen($sStr)
    {
        return mb_strlen($sStr, $this->_sEncoding);
    }

    /**
     * PHP multi byte compliant substr() function wrapper
     *
     * @param string $sStr    value to truncate
     * @param int    $iStart  start position
     * @param int    $iLength length
     *
     * @return string
     */
    public function substr($sStr, $iStart, $iLength = null)
    {
        $iLength = is_null($iLength) ? $this->strlen($sStr) : $iLength;

        return mb_substr($sStr, $iStart, $iLength, $this->_sEncoding);
    }

    /**
     * PHP multi byte compliant strpos() function wrapper
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
        if ($sHaystack && $sNeedle) {
            $iOffset = is_null($iOffset) ? 0 : $iOffset;
            $iPos = mb_strpos($sHaystack, $sNeedle, $iOffset, $this->_sEncoding);
        }

        return $iPos;
    }

    /**
     * PHP multi byte compliant strstr() function wrapper
     *
     * @param string $sHaystack value to search in
     * @param string $sNeedle   value to search for
     *
     * @return string
     */
    public function strstr($sHaystack, $sNeedle)
    {
        // additional check according to bug in PHP 5.2.0 version
        if (!$sHaystack) {
            return false;
        }

        return mb_strstr($sHaystack, $sNeedle, false, $this->_sEncoding);
    }

    /**
     * PHP multi byte compliant strtolower() function wrapper
     *
     * @param string $sString string being lower cased
     *
     * @return string
     */
    public function strtolower($sString)
    {
        return mb_strtolower($sString, $this->_sEncoding);
    }

    /**
     * PHP multi byte compliant strtoupper() function wrapper
     *
     * @param string $sString string being lower cased
     *
     * @return string
     */
    public function strtoupper($sString)
    {
        return mb_strtoupper($sString, $this->_sEncoding);
    }

    /**
     * PHP htmlspecialchars() function wrapper
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function htmlspecialchars($sString, $iQuotStyle = ENT_QUOTES)
    {
        return htmlspecialchars($sString, $iQuotStyle, $this->_sEncoding);
    }

    /**
     * PHP htmlentities() function wrapper
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function htmlentities($sString, $iQuotStyle = ENT_QUOTES)
    {
        return htmlentities($sString, $iQuotStyle, $this->_sEncoding);
    }

    // @codingStandardsIgnoreStart
    /**
     * PHP html_entity_decode() function wrapper
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function html_entity_decode($sString, $iQuotStyle = ENT_QUOTES)
    {
        return html_entity_decode($sString, $iQuotStyle, $this->_sEncoding);
    }

    /**
     * PHP preg_split() function wrapper
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sString  input string
     * @param int    $iLimit   (optional) only sub strings up to limit are returned
     * @param int    $iFlag    flags
     *
     * @return string
     */
    public function preg_split($sPattern, $sString, $iLimit = -1, $iFlag = 0)
    {
        return preg_split($sPattern . 'u', $sString, $iLimit, $iFlag);
    }

    /**
     * PHP preg_replace() function wrapper
     *
     * @param mixed  $aPattern pattern to search for, as a string
     * @param mixed  $sString  string to replace
     * @param string $sSubject strings to search and replace
     * @param int    $iLimit   maximum possible replacements
     * @param int    $iCount   number of replacements done
     *
     * @return string
     */
    public function preg_replace($aPattern, $sString, $sSubject, $iLimit = -1, $iCount = null)
    {
        if (is_array($aPattern)) {
            foreach ($aPattern as &$sPattern) {
                $sPattern = $sPattern . 'u';
            }
        } else {
            $aPattern = $aPattern . 'u';
        }

        return preg_replace($aPattern, $sString, $sSubject, $iLimit, $iCount);
    }

    /**
     * PHP preg_replace() function wrapper
     *
     * @param mixed    $pattern  pattern to search for, as a string
     * @param callable $callback Callback function
     * @param string   $subject  strings to search and replace
     * @param int      $limit    maximum possible replacements
     * @param int      $count    number of replacements done
     *
     * @return string
     */
    public function preg_replace_callback($pattern, $callback, $subject, $limit = -1, &$count = null)
    {
        if (is_array($pattern)) {
            foreach ($pattern as &$item) {
                $item = $item . 'u';
            }
        } else {
            $pattern = $pattern . 'u';
        }

        return preg_replace_callback($pattern, $callback, $subject, $limit, $count);
    }

    /**
     * PHP preg_match() function wrapper
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sSubject input string
     * @param array  $aMatches is filled with the results of search
     * @param int    $iFlags   flags
     * @param int    $iOffset  place from which to start the search
     *
     * @return string
     */
    public function preg_match($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null)
    {
        return preg_match($sPattern . 'u', $sSubject, $aMatches, $iFlags, $iOffset);
    }

    /**
     * PHP preg_match_all() function wrapper
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sSubject input string
     * @param array  $aMatches is filled with the results of search
     * @param int    $iFlags   flags
     * @param int    $iOffset  place from which to start the search
     *
     * @return string
     */
    public function preg_match_all($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null)
    {
        return preg_match_all($sPattern . 'u', $sSubject, $aMatches, $iFlags, $iOffset);
    }
    // @codingStandardsIgnoreEnd

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
    public function wordwrap($sString, $iLength = 75, $sBreak = "\n", $blCut = null)
    {
        if (!$blCut) {
            $sRegexp = "/^(.{1,{$iLength}}\r?(\s|$|\n)|.{1,{$iLength}}[^\r\s\n]*\r?(\n|\s|$))/u";
        } else {
            $sRegexp = "/^([^\s]{{$iLength}}|.{1,{$iLength}}\s)/u";
        }

        $iStrLen = mb_strlen($sString, $this->_sEncoding);
        $iWraps = floor($iStrLen / $iLength);

        $i = $iWraps;
        $sReturn = '';
        $aMatches = [];
        while ($i > 0) {
            $iWraps = floor(mb_strlen($sString, $this->_sEncoding) / $iLength);

            $i = $iWraps;
            if (preg_match($sRegexp, $sString, $aMatches)) {
                $sStr = $aMatches[0];
                $sReturn .= preg_replace('/\s$/s', '', $sStr) . $sBreak;
                $sString = $this->substr($sString, mb_strlen($sStr, $this->_sEncoding));
            } else {
                break;
            }
            $i--;
        }
        $sReturn = preg_replace("/$sBreak$/", '', $sReturn);
        if ($sString) {
            $sReturn .= $sBreak . $sString;
        }

        return $sReturn;
    }

    /**
     * Recodes and returns passed input:
     * if $blToHtmlEntities == true  ä -> &auml;
     * if $blToHtmlEntities == false &auml; -> ä
     *
     * @param string $sInput           text to recode
     * @param bool   $blToHtmlEntities recode direction
     * @param array  $aUmls            language specific characters
     * @param array  $aUmlEntities     language specific characters equivalents in entities form
     *
     * @return string
     */
    public function recodeEntities($sInput, $blToHtmlEntities = false, $aUmls = [], $aUmlEntities = [])
    {
        $aUmls = (count($aUmls) > 0) ? array_merge($this->_aUmls, $aUmls) : $this->_aUmls;
        $aUmlEntities = (count($aUmlEntities) > 0) ? array_merge($this->_aUmlEntities, $aUmlEntities) : $this->_aUmlEntities;

        return $blToHtmlEntities ? str_replace($aUmls, $aUmlEntities, $sInput) : str_replace($aUmlEntities, $aUmls, $sInput);
    }

    /**
     * Checks if string has special chars
     *
     * @param string $sStr string to search in
     *
     * @return bool
     */
    public function hasSpecialChars($sStr)
    {
        return $this->preg_match("/(" . implode("|", $this->_aUmls) . "|(&amp;))/", $sStr);
    }

    /**
     * Replaces special characters with passed char.
     * Special chars are: \n \r \t \xc2\x95 \xc2\xa0 ;
     *
     * @param string $sStr      string to cleanup
     * @param string $sCleanChr which character should be used as a replacement (default is empty space)
     *
     * @return string
     */
    public function cleanStr($sStr, $sCleanChr = ' ')
    {
        return $this->preg_replace("/\n|\r|\t|\xc2\x95|\xc2\xa0|;/", $sCleanChr, $sStr);
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
        return json_encode($data);
    }

    // @codingStandardsIgnoreStart
    /**
     * PHP strip_tags() function wrapper.
     *
     * @param string $sString        the input string
     * @param string $sAllowableTags an optional parameter to specify tags which should not be stripped
     *
     * @return string
     */
    public function strip_tags($sString, $sAllowableTags = '')
    {
        if (stripos($sAllowableTags, '<style>') === false) {
            // strip style tags with definitions within
            $sString = $this->preg_replace("'<style[^>]*>.*</style>'siU", '', $sString);
        }

        return strip_tags($sString, $sAllowableTags);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Compares two strings. Case sensitive.
     * For use in sorting with reverse order
     *
     * @param string $sStr1 String to compare
     * @param string $sStr2 String to compare
     *
     * @return int > 0 if str1 is less than str2; < 0 if str1 is greater than str2, and 0 if they are equal.
     */
    public function strrcmp($sStr1, $sStr2)
    {
        return -strcmp($sStr1, $sStr2);
    }
}
