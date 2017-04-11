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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests language files and templates for missing constants.
 * Slower tests at the end.
 * Tests cases:
 * - language file encoding
 * - identifier checks
 *   -checks that language constants are equal
 *   -checks that map constants are equal
 * - check if all maps are bound to the same translations
 * - check if there are no colons at the end
 * - check if translations are unique and can't be changed.
 * - ensure html entities are not used
 * - make sure all templates have translations
 * - find unused translations. Too long, not to be used for automatic testing.
 */
class Unit_Maintenance_langIntegrityTest extends OxidTestCase
{

    /**
     * Theme to test against
     *
     * @var string
     */
    protected $_sTheme = 'azure';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @return string theme name
     */
    public function getThemeName()
    {
        return $this->_sTheme;
    }

    /**
     * dataProvider with language values
     *
     * @return array
     */
    public function providerLang()
    {
        return array(
            array('de'),
            array('en')
        );
    }

    /**
     * dataProvider with theme values
     *
     * @return array
     */
    public function providerTheme()
    {
        return array(
            array(''),
            array($this->getThemeName()),
            array('admin')
        );
    }

    /**
     * dataProvider with language and theme values without admin values
     *
     * @return array
     */
    public function providerLangTheme()
    {
        return array(
            array('de', ''),
            array('en', ''),
            array('de', $this->getThemeName()),
            array('en', $this->getThemeName())
        );
    }

    /**
     * dataProvider with language, theme and filename values
     *
     * @return array
     */
    public function providerLangThemeFilename()
    {
        return array(
            array('de', '', 'lang.php'),
            array('en', '', 'lang.php'),
            array('de', $this->getThemeName(), 'lang.php'),
            array('en', $this->getThemeName(), 'lang.php'),
            array('de', $this->getThemeName(), 'map.php'),
            array('en', $this->getThemeName(), 'map.php'),
            array('de', 'admin', 'lang.php'),
            array('en', 'admin', 'lang.php')
        );
    }

    /**
     * dataProvider with language and theme values with admin values
     *
     * @return array
     */
    public function providerLangThemeWithAdmin()
    {
        $aDetectOrder = mb_detect_order();
        array_unshift($aDetectOrder, 'ISO-8859-15');

        $sThemeName = $this->getThemeName();

        return array(
            array('de', '', $aDetectOrder),
            array('en', '', $aDetectOrder),
            array('de', $sThemeName, $aDetectOrder),
            array('en', $sThemeName, $aDetectOrder),
            array('de', 'admin', $aDetectOrder),
            array('en', 'admin', $aDetectOrder)
        );
    }

    /**
     * Test if generic language files encoding is correct.
     *
     * @dataProvider providerLangThemeWithAdmin
     */
    public function testLanguageFileEncoding($sLanguage, $sTheme, $aDetectOrder)
    {
        $aLang = $this->_getLanguage($sTheme, $sLanguage);
        $aFileContent = $this->_getLangFileContents($sTheme, $sLanguage, '*.php');

        list($sFileName) = array_keys($aFileContent);
        list($sFileContent) = array_values($aFileContent);

        $this->assertEquals(
            $aLang['charset'], mb_detect_encoding($sFileContent, $aDetectOrder, true),
            "File encoding is equals to charset specified inside the file $sFileName."
        );
        $this->assertEquals(
            utf8_decode($sFileContent), utf8_decode(utf8_decode($sFileContent)), "There are no double UTF-8 encoding in file $sFileName."
        );
        $this->assertEquals(str_replace("\t", "", $sFileContent), $sFileContent, "There are no tab characters in file $sFileName.");
    }

    /**
     * Test if map identifiers are the same.
     *
     */
    public function testMapIdentsMatch()
    {
        $aMapIdentsDE = $this->_getMap($this->getThemeName(), 'de');
        $aMapIdentsEN = $this->_getMap($this->getThemeName(), 'en');

        if (($aMapIdentsDE == array()) || ($aMapIdentsEN == array())) {
            $this->fail(' Map array is empty');
        }

        $this->assertEquals(array(), array_diff_key($aMapIdentsDE, $aMapIdentsEN), 'Ident does not match EN misses some maps');
        $this->assertEquals(array(), array_diff_key($aMapIdentsEN, $aMapIdentsDE), 'Ident does not match DE misses some maps');
    }


    /**
     * Test if generic language file idents are the same.
     *
     * @dataProvider providerTheme
     */
    public function testIdentsMatch($sTheme)
    {
        $aLangIdentsDE = $this->_getLanguage($sTheme, 'de');
        $aLangIdentsEN = $this->_getLanguage($sTheme, 'en');

        $this->assertEquals(array(), array_diff_key($aLangIdentsDE, $aLangIdentsEN), 'ident does not match, EN misses translations');
        $this->assertEquals(array(), array_diff_key($aLangIdentsEN, $aLangIdentsDE), 'ident does not match, DE misses translations');
        $this->assertEquals(count($aLangIdentsDE), count($aLangIdentsEN), 'ident count does not match');
    }


    /**
     * Test if there are'nt any html encodings in language constants
     *
     * @dataProvider providerLangTheme
     */
    public function testNoFrontendHtmlEntitiesAllowed($sLang, $sTheme)
    {
        $aLangIndents = $this->_getLanguage($sTheme, $sLang, '*.php');

        $aLangIndents = str_replace('&amp;', '(amp)', $aLangIndents);
        $aIncorrectIndents = array();

        foreach ($aLangIndents as $sValue) {
            if ($sValue != html_entity_decode($sValue, ENT_COMPAT | ENT_HTML401, 'UTF-8')) {
                $aIncorrectIndents[] = $sValue;
            }
        }
        $this->assertEquals(array(), $aIncorrectIndents, "html entities found. Params: lang - $sLang, theme - $sTheme ");

    }

    /**
     * Tests if maps are bound to the same language constants
     *
     * @depends testMapIdentsMatch
     */
    public function testMapEquality()
    {
        $aMapIdentsDE = $this->_getMap($this->getThemeName(), 'de');
        $aMapIdentsEN = $this->_getMap($this->getThemeName(), 'en');

        if (($aMapIdentsDE == array()) || ($aMapIdentsEN == array())) {
            $this->fail('array is empty');
        }

        foreach ($aMapIdentsEN as $sKey => $sValue) {
            if ($aMapIdentsDE[$sKey] == $sValue) {
                unset($aMapIdentsDE[$sKey]);
            }
        }
        $this->assertEquals(array(), $aMapIdentsDE, 'Maps are bound differently');
    }

    /**
     * Test if there are not any html encodings in map constants.
     *
     * @dataProvider providerLang
     */
    public function testMapNoFrontendHtmlEntitiesAllowed($sLang)
    {
        $aMapIndents = $this->_getMap($this->getThemeName(), 'de');

        if ($aMapIndents == array()) {
            $this->fail(' Map array is empty');
        }

        $aMapIndents = str_replace('&amp;', '(amp)', $aMapIndents);
        $aIncorrectIndents = array();

        foreach ($aMapIndents as $sValue) {
            if ($sValue != html_entity_decode($sValue, ENT_COMPAT | ENT_HTML401, 'UTF-8')) {
                $aIncorrectIndents[] = $sValue;
            }
        }
        $this->assertEquals(array(), $aIncorrectIndents, "html entities found. Params: lang - $sLang ");
    }


    /**
     * Test if mapped constants have translations
     *
     * @dataProvider providerLang
     *
     */
    public function testMapConstantsInGeneric($sLang)
    {
        $aMapIdents = $this->_getMap($this->getThemeName(), $sLang);
        if (array() == $aMapIdents) {
            $this->fail(' Map array is empty');
        }

        $aLangIdents = $this->_getLanguage('', $sLang);
        if (array() == $aLangIdents) {
            $this->fail('Language array is empty');
        }
        $aIncorrectMap = array();

        foreach ($aMapIdents as $sIdent => $sValue) {
            if (!isset($aLangIdents[$sValue])) {
                $aIncorrectMap[$sIdent] = $sValue;
            }
        }
        $this->assertEquals(array(), $aIncorrectMap, "missing translations in generic $sLang file");
    }


    /**
     * Test if there are no colons at the end for strings. It ignores translations equaling to ':'
     * This does not test admin translations
     *
     * @dataProvider providerLangTheme
     */
    public function testColonsAtTheEnd($sLang, $sTheme)
    {

        $aIdents = $this->_getLanguage($sTheme, $sLang);

        $this->assertEquals(array(), $this->_getConstantsWithColons($aIdents), "$sLang has colons. Theme - $sTheme");

    }

    /**
     * Tests that generic translations are not faded out by theme translations
     *
     * @dataProvider providerLang
     */
    public function testThemeTranslationsNotEqualsGenericTranslations($sLang)
    {
        $aGenericTranslations = $this->_getLanguage('', $sLang);
        $aThemeTranslations = $this->_getLanguage($this->getThemeName(), $sLang);
        $aIntersectionsDE = array_intersect_key($aThemeTranslations, $aGenericTranslations);

        $this->assertEquals(array('charset' => 'ISO-8859-15'), $aIntersectionsDE, "some $sLang translations in theme overrides generic translations");
    }

    /**
     *  Tests that all translations are unique
     *
     */
    public function testDuplicates()
    {
        $aThemeTranslationsDE = $this->_getLanguage($this->getThemeName(), 'de');
        $aRTranslationsDE = array_merge($aThemeTranslationsDE, $this->_getLanguage('', 'de'));
        $aTranslationsDE = $this->_stripLangParts($aRTranslationsDE);

        $aThemeTranslationsEN = $this->_getLanguage($this->getThemeName(), 'en');
        $aRTranslationsEN = array_merge($aThemeTranslationsEN, $this->_getLanguage('', 'en'));
        $aTranslationsEN = $this->_stripLangParts($aRTranslationsEN);

        $aStrippedUniqueTranslationsDE = array_unique($aTranslationsDE);
        $aStrippedUniqueTranslationsEN = array_unique($aTranslationsEN);


        $aDifferentKeysDE = array_diff_key($aTranslationsDE, $aStrippedUniqueTranslationsDE);
        $aDifferentKeysEN = array_diff_key($aTranslationsEN, $aStrippedUniqueTranslationsEN);

        $aRTranslationsDE = $this->_excludeByPattern($aRTranslationsDE);
        $aRTranslationsEN = $this->_excludeByPattern($aRTranslationsEN);

        $aDuplicatesDE = array();
        $aDuplicatesEN = array();
        foreach ($aTranslationsDE as $sKey => $sTranslation) {
            if (in_array($sTranslation, $aDifferentKeysDE)) {
                $aDuplicatesDE[$sKey] = $sTranslation;
            }
        }
        foreach ($aTranslationsEN as $sKey => $sTranslation) {
            if (in_array($sTranslation, $aDifferentKeysEN)) {
                $aDuplicatesEN[$sKey] = $sTranslation;
            }
        }
        $aDuplicatesDE = $this->_excludeByPattern($aDuplicatesDE);
        $aDuplicatesEN = $this->_excludeByPattern($aDuplicatesEN);
        asort($aDuplicatesDE);
        asort($aDuplicatesEN);

        $sDuplicates = '';
        $aIntersectionsDE = array_intersect_key($aDuplicatesDE, $aDuplicatesEN);
        $aIntersectionsEN = array_intersect_key($aDuplicatesEN, $aDuplicatesDE);
        $aIntersections = array($aIntersectionsDE, $aIntersectionsEN);

        foreach ($aIntersections as $aIntersection) {
            $sCurTrans = '';
            $iCounter = 0;
            // saving a line, so that we won't print one liners
            $sLineToPrint = '';

            foreach ($aIntersection as $sKey => $sTranslation) {
                if ($sTranslation != '') {
                    if ($sCurTrans != $sTranslation) {
                        $sCurTrans = $sTranslation;
                        if ($iCounter > 1) {
                            $sDuplicates .= "\r\n";
                        }
                        $iCounter = 0;
                        $sLineToPrint = '';
                    }

                    $iCounter++;
                    $sLineToPrint .= "$sKey => " . $aRTranslationsDE[$sKey] . " | " . $aRTranslationsEN[$sKey] . "\r\n";
                    if ($iCounter > 1) {
                        $sDuplicates .= $sLineToPrint;
                        $sLineToPrint = ''; // clearing line
                    }
                }

            }
        }


        $this->assertEquals('', $sDuplicates, 'some translations are duplicated');
    }

    /**
     * Test if there are no missing constant language identifiers in templates.
     * Checking just one version, because above tests checks that both languages have the same identifiers.
     * Dependency added only for map, because can't add dependency on test with data provider.
     * Granted there are workarounds to make it depend on test with data provider, it is not the best practice.
     * So, if testIdentsMatch fails, this test might not give correct results. In such a case, fix idents first!
     *
     * @return null
     */
    public function testMissingTemplateConstants()
    {
        $aTemplateLangIdents = $this->_getTemplateConstants($this->getThemeName());
        $aConstants = array_merge(array_merge($this->_getLanguage('', 'de'), $this->_getMap($this->getThemeName(), 'de')), $this->_getLanguage($this->getThemeName(), 'de'));
        $aConstantLangIdents = array_keys($aConstants);

        $this->assertEquals(array('MONTH_NAME_'), array_values(array_diff($aTemplateLangIdents, $aConstantLangIdents)), 'missing constants in templates');
    }

    /**
     * Test to make sure there are no unused and not needed translations
     *
     */
    public function testNotUsedTranslations()
    {
        $this->markTestSkipped('this test is slow, only to be used locally when checking for translations that are not being used');
        $aUsedConstants = $this->_getTemplateConstants($this->getThemeName());

        $sFile = oxRegistry::getConfig()->getAppDir() . "/translations/de/lang.php";
        include $sFile;


        $aTemp = array_diff(array_keys($aLang), $aUsedConstants);
        $sConstructedFile = oxRegistry::getConfig()->getAppDir() . "/translations/de/constructed_lang.php";
        $sNotUsedFile = oxRegistry::getConfig()->getAppDir() . "/translations/de/notused_lang.php";
        $aExcludeFirst = array();
        if (file_exists($sConstructedFile)) {
            include $sConstructedFile;
            $aExcludeFirst = array_merge($aExcludeFirst, $aLang);
        }
        if (file_exists($sNotUsedFile)) {
            include $sNotUsedFile;
            $aExcludeFirst = array_merge($aExcludeFirst, $aLang);
        }


        $aTemp = array_diff($aTemp, array_keys($aExcludeFirst));
        // got some remaining stuff to check ? check in all files
        if (count($aTemp) > 10) {
            $aTemp = $this->_findUsages($aTemp);
        }
        if (count($aTemp) > 10) {
            $aTemp = $this->_reduceByExcluding($aTemp);
        }

        $this->assertEquals(array('charset'), $aTemp);
    }

    /**
     * Copies constants to a certain file
     *
     * @param        $aConstants
     * @param string $type prefix of filename $type_lang.php
     */
    private function _moveConstants($aConstants, $type = 'constructed')
    {
        $sLocation = oxRegistry::getConfig()->getAppDir() . '/translations/%s/%s_lang.php';
        $aLangs = array('de', 'en');


        foreach ($aLangs as $sLang) {
            $sFile = sprintf($sLocation, $sLang, $type);
            include oxRegistry::getConfig()->getAppDir() . "/translations/$sLang/lang.php";
            $sOutput = "<?php \n //this is generated by langIntegrityTest\n//";
            $sOutput .= $type . '_lang.php' . PHP_EOL . PHP_EOL;

            $sOutput .= '$aLang = array( ' . PHP_EOL;
            foreach ($aConstants as $sKey => $sValue) {
                $sSpaces = space(63 - strlen($sValue));
                $sOutput .= "'$sValue' $sSpaces => \"" . str_replace('"', '\"', $aLang[$sValue]) . "\",\n";
            }

            $sOutput .= "'charset' => 'ISO-8859-15');\n\n";
            $sOutput .= '$sSearch = "/\b' . implode('\b|\b', $aConstants) . '\b/";' . PHP_EOL . PHP_EOL;
            echo $sOutput;
            file_put_contents($sFile, $sOutput);
        }
    }


    /**
     * Reduces array by excluding some values according to pattern
     * This is being done, because some values can't be removed, and this saves the time and effort of writing
     * everything to assertEquals array
     *
     * @param       $aData
     * @param array $aExclusionPatterns
     *
     * @return mixed
     */
    private function _excludeByPattern($aData, $aExclusionPatterns = array())
    {
        // default patterns
        if ($aExclusionPatterns == array()) {
            $aExclusionPatterns[] = '\bOX[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\bERROR_MESSAGE_CONNECTION_[A-Z]*\b';
            $aExclusionPatterns[] = '\bCOLON\b';
            $aExclusionPatterns[] = '\b_UNIT_[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\bMONTH_NAME_[0-9]*\b';
            $aExclusionPatterns[] = '\bPAGE_TITLE_[A-Z0-9_]*\b';
            $aExclusionPatterns[] = '\bDELIVERYTIME[A-Z0-9_]*\b';
        }
        $sSearch = '/' . implode("|", $aExclusionPatterns) . '/';
        $aExcludedConstants = array();
        foreach ($aData as $sKey => $sValue) {
            preg_match($sSearch, $sKey, $match);
            if ($match[0]) {
                $aExcludedConstants[] = $aData[$sKey];
                unset ($aData[$sKey]);
            }
        }

        return $aData;
    }

    /**
     * Reduces array by excluding some values according to pattern
     *
     * @param       $aData
     * @param array $aExclusionPatterns
     *
     * @return mixed
     */
    private function _reduceByExcluding($aData, $aExclusionPatterns = array())
    {
        // default patterns
        if ($aExclusionPatterns == array()) {
            $aExclusionPatterns[] = '\bOX[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\b_UNIT_[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\bMONTH_NAME_[0-9]*\b';
            $aExclusionPatterns[] = '\bPAGE_TITLE_[A-Z0-9_]*\b';
        }
        $sSearch = '/' . implode("|", $aExclusionPatterns) . '/';
        $aExcludedConstants = array();
        foreach ($aData as $key => $sValue) {
            preg_match($sSearch, $sValue, $match);
            if ($match[0]) {
                $aExcludedConstants[] = $aData[$key];
                unset ($aData[$key]);
            }
        }

// these 2 functions copy not used and constructed constants to separate files
//        $this->_moveConstants( $aExcludedConstants );
//        $this->_moveConstants( $aData, "notused" );
        return $aData;
    }

    /** find all files in given path
     *
     * @param array $aIncludeDirs  paths to include in search
     * @param array $aExcludePaths paths to exclude from search
     * @param array $aExtensions   what file extensions to use, default all
     *
     * @return array
     */
    private function _getFiles($aIncludeDirs = array(), $aExcludePaths = array(), $aExtensions = array('*.*'))
    {
        $aFiles = array();
        $aExcludeDirPattern = array();

        // default locations
        if ($aIncludeDirs == array()) {
            $aIncludeDirs[] = oxRegistry::getConfig()->getAppDir() . '../core';
            $aIncludeDirs[] = oxRegistry::getConfig()->getAppDir();
        }
        // default exclude paths
        if ($aExcludePaths == array()) {
            $aExcludeDirPattern[] = '/source/application/translations';
            $aExcludeDirPattern[] = '/source/application/views/admin';
            $aExcludeDirPattern[] = '/source/application/views/' . $this->getThemeName() . '/en';
            $aExcludeDirPattern[] = '/source/application/views/' . $this->getThemeName() . '/de';
        } else {
            $aExcludeDirPattern = $aExcludePaths;
        }


        $aFiles = array();
        $blBreak = false;

        foreach ($aIncludeDirs as $sDir) {
            if (is_dir($sDir)) {
                $aDirs = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($sDir),
                    RecursiveIteratorIterator::SELF_FIRST);

                foreach ($aDirs as $oTplDir) {
                    foreach ($aExcludeDirPattern as $sPattern) {
                        if (strpos($oTplDir->getRealpath(), $sPattern) != false) {
                            $blBreak = true;
                            break;
                        }
                    }
                    if ($oTplDir->isDir() && !$blBreak) {
                        foreach ($aExtensions as $sExtension) {
                            $aFiles = array_merge($aFiles, glob($oTplDir->getRealpath() . DIRECTORY_SEPARATOR . $sExtension));
                        }
                    }
                    $blBreak = false;
                }
                // adds files from base dir, ex.: /mnt/~...~www/
                foreach ($aExtensions as $sExtension) {
                    $aFiles = array_merge($aFiles, glob($sDir . DIRECTORY_SEPARATOR . $sExtension));
                }
            }
        }

        return $aFiles;
    }


    /**
     * Find if given array constants are found anywhere in source
     *  this check will be slow
     *
     * @param $aConstants array
     *
     * @return array
     */
    private function _findUsages($aConstants)
    {
        $aFiles = $this->_getFiles(array(), array(), array('*.php'));
        $aUsages = array();
        $sSearch = '/\b' . implode('\b|\b', $aConstants) . '\b/';

        foreach ($aFiles as $sFile) {
            $sTpl = file_get_contents($sFile);
            preg_match_all($sSearch, $sTpl, $aMatches);

            $aUsages = array_merge($aMatches[0], $aUsages);

            foreach ($aMatches[0] as $sMatch) {
                $sSearch = str_replace("\b$sMatch\b", '', $sSearch);
                $sSearch = str_replace(array('||', '|/', '/|'), array('|', '/', '/'), $sSearch);
            }

            if ($sSearch == '//') {
                break;
            }
        }

        $aResults = array_diff($aConstants, array_unique($aUsages));

        return $aResults;

    }

    /**
     * Removes parts from the constants, colons(:) by default,
     * anything else you want, added by parameters
     *
     * @param $aTranslations
     *
     * @return mixed
     */
    private function _stripLangParts($aTranslations)
    {
        $aLangParts = array(':');
        $aStrippedTranslations = str_replace($aLangParts, '', $aTranslations);

        return $aStrippedTranslations;
    }

    /**
     * Get all constants that have colons at the end
     *
     * @param $aLang
     *
     * @return array
     */
    private function _getConstantsWithColons($aLang)
    {
        $aColonArray = array();
        foreach ($aLang as $key => $sTranslation) {
            if (substr($sTranslation, -1) == ':' && $sTranslation != ':') {
                $aColonArray[$key] = $sTranslation;
            }
        }

        return $aColonArray;
    }

    /**
     * Get language array by given theme, shop and language.
     *
     * @param string $sTheme       theme name
     * @param string $sLang        languge abbr
     * @param string $sFilePattern pattern
     *
     * @return array
     */
    private function _getLangFileContents($sTheme, $sLang, $sFilePattern = '*lang.php')
    {
        $aFileContent = array();
        $sMask = $sFile = $this->_getLanguageFilePath($sTheme, $sLang, $sFilePattern);
        foreach (glob($sMask) as $sFile) {
            if (is_readable($sFile)) {
                include $sFile;
                $aFileContent[$sFile] .= file_get_contents($sFile) . PHP_EOL . PHP_EOL;
            }
        }

        return $aFileContent;
    }

    /**
     * Get specific map by given theme and language
     *
     * @param string $sTheme theme name
     * @param string $sLang  language abbreviation
     *
     * @return array
     */
    private function _getMap($sTheme, $sLang)
    {
        $sFile = oxRegistry::getConfig()->getAppDir() . "views/$sTheme/$sLang/map.php";
        if (is_readable($sFile)) {
            include $sFile;

            return $aMap;
        }

        return array();
    }

    /**
     * Get specific language by given theme, language abbreviation and filename
     *
     * @param string $sTheme    theme name
     * @param string $sLang     language abbreviation
     * @param string $sFileName lang file name
     *
     * @return array
     */
    private function _getLanguage($sTheme, $sLang, $sFileName = "lang.php")
    {
        $aAllLang = array();
        $sInputFile = $this->_getLanguageFilePath($sTheme, $sLang, $sFileName);
        if (is_readable($sInputFile)) {
            include $sInputFile;

            return $aLang;
        }

        // if we give pattern, not a direct file, do the search
        foreach (glob($sInputFile) as $sFile) {
            if (is_readable($sFile)) {
                include $sFile;
                $aAllLang = array_merge($aAllLang, $aLang);
            }
        }
        if (array() == $aAllLang) {
            echo $sFile . ' cannot be read' . PHP_EOL;
        }

        return $aAllLang;
    }

    /**
     * Returns path to language file
     *
     * @param $sType
     * @param $sLang
     * @param $sFile
     *
     * @return string pathname
     */
    private function _getLanguageFilePath($sType, $sLang, $sFile)
    {
        if ($sType == '') {
            $sDir = oxRegistry::getConfig()->getAppDir() . '/translations' . DIRECTORY_SEPARATOR . $sLang . DIRECTORY_SEPARATOR . $sFile;
        } elseif ($sType == 'setup') {
            $sDir = oxRegistry::getConfig()->getConfigParam('sShopDir') . '/setup' . DIRECTORY_SEPARATOR . $sLang . DIRECTORY_SEPARATOR . $sFile;
        } else {
            $sDir = oxRegistry::getConfig()->getAppDir() . '/views' . DIRECTORY_SEPARATOR . $sType . DIRECTORY_SEPARATOR . $sLang . DIRECTORY_SEPARATOR . $sFile;
        }

        return $sDir;
    }

    /**
     * Get theme templates.
     *
     * @param string $sTheme theme name
     *
     * @return array template file array
     */
    private function _getTemplates($sTheme)
    {
        $sDir = oxRegistry::getConfig()->getAppDir() . "views/$sTheme/tpl";
        $aTemplates = array();

        if (is_dir($sDir)) {
            $aDirs = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sDir),
                RecursiveIteratorIterator::SELF_FIRST);

            foreach ($aDirs as $oTplDir) {
                if ($oTplDir->isDir()) {
                    $aTemplates = array_merge($aTemplates, glob($oTplDir->getRealpath() . DIRECTORY_SEPARATOR . "*.tpl"));
                }
            }
        }

        return $aTemplates;
    }

    /**
     * Get used language constants in given template set (parsing *.tpl files).
     *
     * @param string $sTheme theme name
     *
     * @return array
     */
    private function _getTemplateConstants($sTheme = 'azure')
    {
        $aLang = array();

        $aTemplates = $this->_getTemplates($sTheme);

        if (count($aTemplates) == 0) {
            echo '_getTemplateConstants: Didn\'t find any templates.';
        }

        foreach ($aTemplates as $tpl) {
            $sTpl = file_get_contents($tpl);
            $sReg = '/oxmultilang +ident="([A-Z\_0-9]+)"/i';
            preg_match_all($sReg, $sTpl, $aMatches);

            foreach ($aMatches[1] as $sConst) {
                $aLang[] = $sConst;
            }

            $sReg = '/"([A-Z\_0-9]+)"\|oxmultilangassign/i';
            preg_match_all($sReg, $sTpl, $aMatches);

            foreach ($aMatches[1] as $sConst) {
                $aLang[] = $sConst;
            }
        }

        if (count($aLang) == 0) {
            echo '_getTemplateConstants: array is empty, check if directories are correctly set in the method.';
        }

        return array_unique($aLang);
    }

    /**
     * dataProvider with files for invalid encoding detection.
     *
     * @return array
     */
    public function providerLanguageFilesForInvalidEncoding()
    {
        return array(
            array('de', '', '*.php'),
            array('en', '', '*.php'),
            array('de', $this->getThemeName(), '*.php'),
            array('en', $this->getThemeName(), '*.php'),
            array('de', 'admin', '*.php'),
            array('en', 'admin', '*.php'),
            array('de', 'setup', 'lang.php'),
            array('en', 'setup', 'lang.php'),
        );
    }

    /**
     * Test if generic files don't have invalid encoding.
     *
     * @dataProvider providerLanguageFilesForInvalidEncoding
     */
    public function testLanguageFilesForInvalidEncoding($sLanguage, $sType, $sFilePattern)
    {
        $aFileContent = $this->_getLangFileContents($sType, $sLanguage, $sFilePattern);

        list($sFileName) = array_keys($aFileContent);
        list($sFileContent) = array_values($aFileContent);

        foreach (array(0xEF, 0xBB, 0xBF, 0x9C) as $sCharacter) {
            if (strpos($sFileContent, $sCharacter) !== false) {
                $this->fail("Character with invalid encoding found in $sFileName file.");
            }
        }
    }

    /**
     * Data provider with sql files for invalid encoding detection.
     *
     * @return array
     */
    public function providerSqlFilesForInvalidEncoding()
    {
        return array(
            array(getShopBasePath() . '/setup/sql' . OXID_VERSION_SUFIX . '/*.sql'),
        );
    }

    /**
     * Test if sql files don't have invalid encoding.
     *
     * @dataProvider providerSqlFilesForInvalidEncoding
     */
    public function testSqlFilesForInvalidEncoding($sFilePathPattern)
    {
        foreach (glob($sFilePathPattern) as $sFilePath) {
            if (is_readable($sFilePath)) {
                $sFileContent = file_get_contents($sFilePath);
                foreach (array(0xEF, 0xBB, 0xBF, 0x9C) as $sCharacter) {
                    $this->assertFalse(strpos($sFileContent, $sCharacter), "Character with invalid encoding found in {$sFilePath} file.");
                }
            }
        }
    }
}

/**
 * Recursive space placement
 * Simply for formatting the output easier
 */
function space($amount)
{
    if ($amount <= 0) {
        return ' ';
    }

    return ' ' . space($amount - 1);
}
