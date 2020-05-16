<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Factory class responsible for redirecting string handling functions to specific
 * string handling class. String handler basically is intended for dealing with multibyte string
 * and is NOT supposed to replace all string handling functions.
 * We use the handler for shop data and user input, but prefer not to use it for ascii strings
 * (eg. field or file names).
 */
interface IStr
{

    /**
     * Get string length
     *
     * @param string $sStr string to measure its length
     *
     * @return int
     */
    public function strlen($sStr);

    /**
     * Return part of a string
     *
     * @param string $sStr    value to truncate
     * @param int    $iStart  start position
     * @param int    $iLength length
     *
     * @return string
     */
    public function substr($sStr, $iStart, $iLength = null);

    /**
     * Find the position of the first occurrence of a substring in a string
     *
     * @param string $sHaystack value to search in
     * @param string $sNeedle   value to search for
     * @param int    $iOffset   initial search position
     *
     * @return string
     */
    public function strpos($sHaystack, $sNeedle, $iOffset = null);

    /**
     *  Find the first occurrence of a string
     *
     * @param string $sHaystack value to search in
     * @param string $sNeedle   value to search for
     *
     * @return string
     */
    public function strstr($sHaystack, $sNeedle);

    /**
     * Make a string lowercase
     *
     * @param string $sString string being lower cased
     *
     * @return string
     */
    public function strtolower($sString);

    /**
     * Make a string uppercase
     *
     * @param string $sString string being lower cased
     *
     * @return string
     */
    public function strtoupper($sString);

    /**
     * Convert special characters to HTML entities
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function htmlspecialchars($sString, $iQuotStyle = ENT_QUOTES);

    /**
     * Convert all applicable characters to HTML entities
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function htmlentities($sString, $iQuotStyle = ENT_QUOTES);

    // @codingStandardsIgnoreStart

    /**
     * Convert HTML entities to their corresponding characters
     *
     * @param string $sString    string being converted
     * @param int    $iQuotStyle quoting rule
     *
     * @return string
     */
    public function html_entity_decode($sString, $iQuotStyle = ENT_QUOTES);

    /**
     * Split string by a regular expression
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sString  input string
     * @param int    $iLimit   (optional) only sub strings up to limit are returned
     * @param int    $iFlag    flags
     *
     * @return string
     */
    public function preg_split($sPattern, $sString, $iLimit = -1, $iFlag = 0);

    /**
     * Perform a regular expression search and replace
     *
     * @param mixed  $aPattern pattern to search for, as a string
     * @param mixed  $sString  string to replace
     * @param string $sSubject strings to search and replace
     * @param int    $iLimit   maximum possible replacements
     * @param int    $iCount   number of replacements done
     *
     * @return string
     */
    public function preg_replace($aPattern, $sString, $sSubject, $iLimit = -1, $iCount = null);

    /**
     * Perform a regular expression search and replace using a callback
     *
     * @param mixed    $pattern  pattern to search for, as a string
     * @param callable $callback Callback function
     * @param string   $subject  strings to search and replace
     * @param int      $limit    maximum possible replacements
     * @param int      $count    number of replacements done
     *
     * @return string
     */
    public function preg_replace_callback($pattern, $callback, $subject, $limit = -1, &$count = null);

    /**
     * Perform a regular expression match
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sSubject input string
     * @param array  $aMatches is filled with the results of search
     * @param int    $iFlags   flags
     * @param int    $iOffset  place from which to start the search
     *
     * @return string
     */
    public function preg_match($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null);

    /**
     * Perform a global regular expression match
     *
     * @param string $sPattern pattern to search for, as a string
     * @param string $sSubject input string
     * @param array  $aMatches is filled with the results of search
     * @param int    $iFlags   flags
     * @param int    $iOffset  place from which to start the search
     *
     * @return string
     */
    public function preg_match_all($sPattern, $sSubject, &$aMatches = null, $iFlags = null, $iOffset = null);

    /**
     * Make a string's first character uppercase
     *
     * @param string $sSubject input string
     *
     * @return string
     */
    public function ucfirst($sSubject);

    /**
     * Wraps a string to a given number of characters
     *
     * @param string $sString input string
     * @param int    $iLength column width
     * @param string $sBreak  line is broken using the optional break parameter
     * @param bool   $blCut   string is always wrapped at the specified width
     *
     * @return string
     */
    public function wordwrap($sString, $iLength = 75, $sBreak = "\n", $blCut = null);

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
    public function recodeEntities($sInput, $blToHtmlEntities = false, $aUmls = [], $aUmlEntities = []);

    /**
     * Checks if string has special chars
     *
     * @param string $sStr string to search in
     *
     * @return bool
     */
    public function hasSpecialChars($sStr);

    /**
     * Replaces special characters with passed char.
     * Special chars are: \n \r \t \xc2\x95 \xc2\xa0 ;
     *
     * @param string $sStr      string to cleanup
     * @param string $sCleanChr which character should be used as a replacement (default is empty space)
     *
     * @return string
     */
    public function cleanStr($sStr, $sCleanChr = ' ');

    /**
     * wrapper for json encode, which does not work with non utf8 characters
     *
     * @param mixed $data data to encode
     *
     * @return string
     */
    public function jsonEncode($data);

    // @codingStandardsIgnoreStart

    /**
     * Strip HTML and PHP tags from a string
     *
     * @param string $sString        the input string
     * @param string $sAllowableTags an optional parameter to specify tags which should not be stripped
     *
     * @return string
     */
    public function strip_tags($sString, $sAllowableTags = '');

    /**
     * Compares two strings. Case sensitive.
     * For use in sorting with reverse order
     *
     * @param string $sStr1 String to compare
     * @param string $sStr2 String to compare
     *
     * @return int > 0 if str1 is less than str2; < 0 if str1 is greater than str2, and 0 if they are equal.
     */
    public function strrcmp($sStr1, $sStr2);

    /**
     * Checks if $haystack begins with $needle
     * (If $needle is longer than $haystack, it returns false)
     *
     * @param string $haystack value to search in
     * @param string $needle   value to search for
     *
     * @return bool
     */
    public function str_starts_with(string $haystack, string $needle);

    /**
     * Checks if $haystack ends with $needle
     * (If $needle is longer than $haystack, it returns false)
     *
     * @param string $haystack value to search in
     * @param string $needle   value to search for
     *
     * @return bool
     */
    public function str_ends_with(string $haystack, string $needle);

    /**
     * Returns true if $needle is found in $haystack
     *
     * @param string $haystack value to search in
     * @param string $needle   value to search for
     *
     * @return bool
     */
    public function str_contains(string $haystack, string $needle);
}