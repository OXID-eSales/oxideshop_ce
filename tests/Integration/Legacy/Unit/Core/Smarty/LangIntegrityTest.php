<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

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
class LangIntegrityTest extends \OxidTestCase
{
    use ContainerTrait;
    /**
     * Theme to test against
     *
     * @var string
     */
    protected $_sTheme = 'azure';

    /**
     * @return string theme name
     */
    private function getThemeName()
    {
        return $this->_sTheme;
    }

    /**
     * @return string admin theme name
     */
    private function getAdminThemeName()
    {
        return $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();
    }

    /**
     * dataProvider with language values
     *
     * @return array
     */
    public function providerLang()
    {
        return [['de'], ['en']];
    }

    /**
     * dataProvider with theme values
     *
     * @return array
     */
    public function providerTheme()
    {
        return [[''], [$this->getThemeName()], [$this->getAdminThemeName()]];
    }

    /**
     * dataProvider with language and theme values without admin values
     *
     * @return array
     */
    public function providerLangTheme()
    {
        return [['de', ''], ['en', ''], ['de', $this->getThemeName()], ['en', $this->getThemeName()]];
    }

    /**
     * dataProvider with language, theme and filename values
     *
     * @return array
     */
    public function providerLangThemeFilename()
    {
        return [['de', '', 'lang.php'], ['en', '', 'lang.php'], ['de', $this->getThemeName(), 'lang.php'], ['en', $this->getThemeName(), 'lang.php'], ['de', $this->getThemeName(), 'map.php'], ['en', $this->getThemeName(), 'map.php'], ['de', $this->getAdminThemeName(), 'lang.php'], ['en', $this->getAdminThemeName(), 'lang.php']];
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
        //make mb_detect_encoding telling us 'UTF-8' even if the string could be represented with an other compatible
        //charset. Because for our unittest it is important to know if a string is valid utf-8.
        array_unshift($aDetectOrder, 'UTF-8');

        $sThemeName = $this->getThemeName();

        return [['de', '', $aDetectOrder], ['en', '', $aDetectOrder], ['de', $sThemeName, $aDetectOrder], ['en', $sThemeName, $aDetectOrder], ['de', $this->getAdminThemeName(), $aDetectOrder], ['en', $this->getAdminThemeName(), $aDetectOrder]];
    }

    /**
     * Test if generic language files encoding is correct.
     *
     * @dataProvider providerLangThemeWithAdmin
     */
    public function testLanguageFileEncoding($sLanguage, $sTheme, $aDetectOrder)
    {
        $aLang = $this->getLanguageFileContents($sTheme, $sLanguage);

        //oxid lang encoding should stay on utf-8 because all language files are utf-8 encoded,
        //or at least converted to utf8
        $this->assertEquals('UTF-8', $aLang['charset'], "non utf8 for language $sLanguage in theme $sTheme");

        $aFileContent = $this->getLangFileContents($sTheme, $sLanguage, '*.php');

        [$sFileName] = array_keys($aFileContent);
        [$sFileContent] = array_values($aFileContent);

        //check for unicode replacement character because it appears when some re-encodings went wrong
        //https://en.wikipedia.org/wiki/Specials_(Unicode_block)#Replacement_character
        $posOfIlligalChar = strpos((string) $sFileContent, "�");
        $this->assertTrue(
            $posOfIlligalChar === false,
            "there is an unicode replacement character in file $sFileName in Line at $posOfIlligalChar"
        );

        //converting from utf-8 into utf-8 will fail if there is anything wrong with that string
        $this->assertEquals(
            iconv('UTF-8', 'UTF-8', (string) $sFileContent),
            $sFileContent,
            "there is an invalid unicode character in file $sFileName "
        );

        $this->assertEquals(
            $aLang['charset'],
            mb_detect_encoding((string) $sFileContent, $aDetectOrder, true),
            "File encoding does not equal charset specified inside the file $sFileName."
        );

        //converting utf8 to ISO-8859-1 to check if there are double encodings in the next step
        $sISO88591 = utf8_decode((string) $sFileContent);
        $this->assertFalse(
            mb_detect_encoding($sISO88591) === 'UTF-8',
            "There are double UTF-8 encoding in file $sFileName."
        );

        $this->assertEquals(
            mb_ereg_replace("\t", "", (string) $sFileContent),
            $sFileContent,
            "There are tab characters in file $sFileName."
        );
    }

    /**
     * Test if map identifiers are the same.
     *
     */
    public function testMapIdentsMatch()
    {
        $aMapIdentsDE = $this->getMap($this->getThemeName(), 'de');
        $aMapIdentsEN = $this->getMap($this->getThemeName(), 'en');

        if (($aMapIdentsDE == []) || ($aMapIdentsEN == [])) {
            $this->fail(' Map array is empty');
        }

        $this->assertEquals([], array_diff_key($aMapIdentsDE, $aMapIdentsEN), 'Ident does not match EN misses some maps');
        $this->assertEquals([], array_diff_key($aMapIdentsEN, $aMapIdentsDE), 'Ident does not match DE misses some maps');
    }

    /**
     * Test if generic language file idents are the same.
     *
     * @dataProvider providerTheme
     */
    public function testIdentsMatch($sTheme)
    {
        $aLangIdentsDE = $this->getLanguageFileContents($sTheme, 'de');
        $aLangIdentsEN = $this->getLanguageFileContents($sTheme, 'en');

        $this->assertEquals([], array_diff_key($aLangIdentsDE, $aLangIdentsEN), 'ident does not match, EN misses translations');
        $this->assertEquals([], array_diff_key($aLangIdentsEN, $aLangIdentsDE), 'ident does not match, DE misses translations');
        $this->assertEquals(count($aLangIdentsDE), count($aLangIdentsEN), 'ident count does not match');
    }

    /**
     * Test if there are'nt any html encodings in language constants
     *
     * @dataProvider providerLangTheme
     */
    public function testNoFrontendHtmlEntitiesAllowed($sLang, $sTheme)
    {
        $aLangIndents = $this->getLanguageFileContents($sTheme, $sLang, '*.php');

        $aLangIndents = str_replace('&amp;', '(amp)', $aLangIndents);
        $aIncorrectIndents = [];

        foreach ($aLangIndents as $sValue) {
            if ($sValue != html_entity_decode((string) $sValue, ENT_COMPAT | ENT_HTML401, 'UTF-8')) {
                $aIncorrectIndents[] = $sValue;
            }
        }
        $this->assertEquals([], $aIncorrectIndents, "html entities found. Params: lang - $sLang, theme - $sTheme ");
    }

    /**
     * Tests if maps are bound to the same language constants
     *
     * @depends testMapIdentsMatch
     */
    public function testMapEquality()
    {
        $aMapIdentsDE = $this->getMap($this->getThemeName(), 'de');
        $aMapIdentsEN = $this->getMap($this->getThemeName(), 'en');

        if (($aMapIdentsDE == []) || ($aMapIdentsEN == [])) {
            $this->fail('array is empty');
        }

        foreach ($aMapIdentsEN as $sKey => $sValue) {
            if ($aMapIdentsDE[$sKey] == $sValue) {
                unset($aMapIdentsDE[$sKey]);
            }
        }
        $this->assertEquals([], $aMapIdentsDE, 'Maps are bound differently');
    }

    /**
     * Test if there are not any html encodings in map constants.
     *
     * @dataProvider providerLang
     */
    public function testMapNoFrontendHtmlEntitiesAllowed($sLang)
    {
        $aMapIndents = $this->getMap($this->getThemeName(), 'de');

        if ($aMapIndents == []) {
            $this->fail(' Map array is empty');
        }

        $aMapIndents = str_replace('&amp;', '(amp)', $aMapIndents);
        $aIncorrectIndents = [];

        foreach ($aMapIndents as $sValue) {
            if ($sValue != html_entity_decode((string) $sValue, ENT_COMPAT | ENT_HTML401, 'UTF-8')) {
                $aIncorrectIndents[] = $sValue;
            }
        }
        $this->assertEquals([], $aIncorrectIndents, "html entities found. Params: lang - $sLang ");
    }

    /**
     * Test if mapped constants have translations
     *
     * @dataProvider providerLang
     *
     */
    public function testMapConstantsInGeneric($sLang)
    {
        $aMapIdents = $this->getMap($this->getThemeName(), $sLang);
        if ([] == $aMapIdents) {
            $this->fail(' Map array is empty');
        }

        $aLangIdents = $this->getLanguageFileContents('', $sLang);
        if ([] == $aLangIdents) {
            $this->fail('Language array is empty');
        }
        $aIncorrectMap = [];

        foreach ($aMapIdents as $sIdent => $sValue) {
            if (!isset($aLangIdents[$sValue])) {
                $aIncorrectMap[$sIdent] = $sValue;
            }
        }
        $this->assertEquals([], $aIncorrectMap, "missing translations in generic $sLang file");
    }

    /**
     * Test if there are no colons at the end for strings. It ignores translations equaling to ':'
     * This does not test admin translations
     *
     * @dataProvider providerLangTheme
     */
    public function testColonsAtTheEnd($sLang, $sTheme)
    {
        $aIdents = $this->getLanguageFileContents($sTheme, $sLang);

        $this->assertEquals([], $this->getConstantsWithColons($aIdents), "$sLang has colons. Theme - $sTheme");
    }

    /**
     * Tests that generic translations are not faded out by theme translations
     *
     * @dataProvider providerLang
     */
    public function testThemeTranslationsNotEqualsGenericTranslations($sLang)
    {
        $aGenericTranslations = $this->getLanguageFileContents('', $sLang);
        $aThemeTranslations = $this->getLanguageFileContents($this->getThemeName(), $sLang);
        $aIntersectionsDE = array_intersect_key($aThemeTranslations, $aGenericTranslations);

        $this->assertEquals(['charset' => 'UTF-8'], $aIntersectionsDE, "some $sLang translations in theme overrides generic translations");
    }

    /**
     *  Tests that all translations are unique
     *
     */
    public function testDuplicates()
    {
        $aThemeTranslationsDE = $this->getLanguageFileContents($this->getThemeName(), 'de');
        $aRTranslationsDE = array_merge($aThemeTranslationsDE, $this->getLanguageFileContents('', 'de'));
        $aTranslationsDE = $this->stripLangParts($aRTranslationsDE);

        $aThemeTranslationsEN = $this->getLanguageFileContents($this->getThemeName(), 'en');
        $aRTranslationsEN = array_merge($aThemeTranslationsEN, $this->getLanguageFileContents('', 'en'));
        $aTranslationsEN = $this->stripLangParts($aRTranslationsEN);

        $aStrippedUniqueTranslationsDE = array_unique($aTranslationsDE);
        $aStrippedUniqueTranslationsEN = array_unique($aTranslationsEN);


        $aDifferentKeysDE = array_diff_key($aTranslationsDE, $aStrippedUniqueTranslationsDE);
        $aDifferentKeysEN = array_diff_key($aTranslationsEN, $aStrippedUniqueTranslationsEN);

        $aRTranslationsDE = $this->excludeByPattern($aRTranslationsDE);
        $aRTranslationsEN = $this->excludeByPattern($aRTranslationsEN);

        $aDuplicatesDE = [];
        $aDuplicatesEN = [];
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
        $aDuplicatesDE = $this->excludeByPattern($aDuplicatesDE);
        $aDuplicatesEN = $this->excludeByPattern($aDuplicatesEN);
        asort($aDuplicatesDE);
        asort($aDuplicatesEN);

        $sDuplicates = '';
        $aIntersectionsDE = array_intersect_key($aDuplicatesDE, $aDuplicatesEN);
        $aIntersectionsEN = array_intersect_key($aDuplicatesEN, $aDuplicatesDE);
        $aIntersections = [$aIntersectionsDE, $aIntersectionsEN];

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
     * Reduces array by excluding some values according to pattern
     * This is being done, because some values can't be removed, and this saves the time and effort of writing
     * everything to assertEquals array
     *
     * @param array $aData
     * @param array $aExclusionPatterns
     *
     * @return mixed
     */
    private function excludeByPattern($aData, $aExclusionPatterns = [])
    {
        // default patterns
        if ($aExclusionPatterns == []) {
            $aExclusionPatterns[] = '\bOX[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\bERROR_MESSAGE_CONNECTION_[A-Z]*\b';
            $aExclusionPatterns[] = '\bCOLON\b';
            $aExclusionPatterns[] = '\b_UNIT_[A-Z0-9]*\b';
            $aExclusionPatterns[] = '\bMONTH_NAME_[0-9]*\b';
            $aExclusionPatterns[] = '\bPAGE_TITLE_[A-Z0-9_]*\b';
            $aExclusionPatterns[] = '\bDELIVERYTIME[A-Z0-9_]*\b';
        }
        $sSearch = '/' . implode("|", $aExclusionPatterns) . '/';
        $aExcludedConstants = [];
        foreach ($aData as $sKey => $sValue) {
            preg_match($sSearch, $sKey, $match);
            if ($match[0]) {
                $aExcludedConstants[] = $aData[$sKey];
                unset($aData[$sKey]);
            }
        }

        return $aData;
    }

    /**
     * Removes parts from the constants, colons(:) by default,
     * anything else you want, added by parameters
     *
     * @param array $aTranslations
     *
     * @return mixed
     */
    private function stripLangParts($aTranslations)
    {
        $aLangParts = [':'];
        $aStrippedTranslations = str_replace($aLangParts, '', $aTranslations);

        return $aStrippedTranslations;
    }

    /**
     * Get all constants that have colons at the end
     *
     * @param array $aLang
     *
     * @return array
     */
    private function getConstantsWithColons($aLang)
    {
        $aColonArray = [];
        foreach ($aLang as $key => $sTranslation) {
            if (str_ends_with((string) $sTranslation, ':') && $sTranslation != ':') {
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
    private function getLangFileContents($sTheme, $sLang, $sFilePattern = '*lang.php')
    {
        $aFileContent = [];
        $sMask = $this->getLanguageFilePath($sTheme, $sLang, $sFilePattern);
        foreach (glob($sMask) as $sFile) {
            if (is_readable($sFile)) {
                include $sFile;
                $aFileContent[$sFile] = file_get_contents($sFile) . PHP_EOL . PHP_EOL;
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
    private function getMap($sTheme, $sLang)
    {
        $aMap = [];
        $sFile = $this->getConfig()->getAppDir() . "views/$sTheme/$sLang/map.php";
        if (is_readable($sFile)) {
            include $sFile;
        }

        return $aMap;
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
    private function getLanguageFileContents($sTheme, $sLang, $sFileName = "lang.php"): array
    {
        $aAllLang = [];
        $sInputFile = $this->getLanguageFilePath($sTheme, $sLang, $sFileName);
        if (is_readable($sInputFile)) {
            $aLang = [];
            include $sInputFile;
            return $aLang;
        }

        // if we give pattern, not a direct file, do the search
        foreach (glob($sInputFile) as $sFile) {
            if (is_readable($sFile)) {
                $aLang = [];
                include $sFile;
                $aAllLang = array_merge($aAllLang, $aLang);
            }
        }
        if ([] == $aAllLang) {
            echo $sFile . ' cannot be read' . PHP_EOL;
        }

        return $aAllLang;
    }

    /**
     * Generates the full absolute path to a language file.
     *
     * @param string $type Language file type
     * @param string $languageCode Language code in form of ISO 639-1
     * @param string $fileName File name part for the language file (might include partial path)
     *
     * @return string Full absolute path to a language file
     */
    private function getLanguageFilePath($type, $languageCode, $fileName)
    {
        $applicationDirectory = rtrim((string) $this->getConfig()->getAppDir(), DIRECTORY_SEPARATOR);
        $shopDirectory = rtrim((string) $this->getConfig()->getConfigParam('sShopDir'), DIRECTORY_SEPARATOR);

        if (empty($type)) {
            $pathItems = [$applicationDirectory, 'translations', $languageCode, $fileName];
        } elseif (strtolower($type) === 'setup') {
            $pathItems = [$shopDirectory, $type, $languageCode, $fileName];
        } else {
            $pathItems = [$applicationDirectory, 'views', $type, $languageCode, $fileName];
        }

        $filePath = implode(DIRECTORY_SEPARATOR, $pathItems);

        return $filePath;
    }

    /**
     * Get theme templates.
     *
     * @param string $sTheme theme name
     *
     * @return array template file array
     */
    private function getTemplates($sTheme)
    {
        $sDir = $this->getConfig()->getAppDir() . "views/$sTheme/tpl";
        $aTemplates = [];

        if (is_dir($sDir)) {
            $aDirs = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sDir),
                RecursiveIteratorIterator::SELF_FIRST
            );

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
    private function getTemplateConstants($sTheme = 'azure')
    {
        $aLang = [];

        $aTemplates = $this->getTemplates($sTheme);

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
        return [['de', '', '*.php'], ['en', '', '*.php'], ['de', $this->getThemeName(), '*.php'], ['en', $this->getThemeName(), '*.php'], ['de', $this->getAdminThemeName(), '*.php'], ['en', $this->getAdminThemeName(), '*.php'], ['De', 'Setup', 'lang.php'], ['En', 'Setup', 'lang.php']];
    }

    /**
     * Look for language files that are declared as being non UTF-8 (ASCII or ISO-8859-15) encoded files
     * and check that they do not contain UTF-8 characters.
     *
     * @param string $languageCode Language code in form of ISO 639-1
     * @param string $type Language file type
     * @param string $filePattern File glob pattern to match
     *
     * @dataProvider providerLanguageFilesForInvalidEncoding
     */
    public function testLanguageFilesForInvalidEncoding($languageCode, $type, $filePattern)
    {
        $languageFiles = $this->getLangFileContents($type, $languageCode, $filePattern);

        foreach ($languageFiles as $filePath => $fileContent) {
            $languageTranslation = $this->getLanguageFileContents($type, $languageCode, $filePattern);

            $declaredEncoding = $languageTranslation['charset'];
            $isDeclaredAsUTF8 = strtolower((string) $declaredEncoding) === 'utf-8';

            if ($isDeclaredAsUTF8) {
                //find illegal unicode sequences
                //http://stackoverflow.com/questions/6723562/how-to-detect-malformed-utf-8-string-in-php
                //http://unicode.org/faq/utf_bom.html
                $isValidEncoding = mb_check_encoding($fileContent, 'UTF-8');
                $errorMessage = "invalid utf8 encoding detected in $filePath";
            } else {
                $isInvalidEncoding = static::isUTF8CharacterPresentInContent($fileContent);
                $isValidEncoding = !$isInvalidEncoding;
                $errorMessage = $this->getErrorMessageForTestLanguageFilesForInvalidEncoding($filePath, $declaredEncoding);
            }

            $this->assertTrue($isValidEncoding, $errorMessage);
        }
    }

    /**
     * Helper function to detect UTF-8 character presence in given content.
     *
     * @TODO: Transfer to testing library
     *
     * @param string $content Content to be checked for UTF-8 characters
     *
     * @return bool True in case there is at least one UTF-8 character, false otherwise
     */
    public static function isUTF8CharacterPresentInContent($content)
    {
        return mb_detect_encoding($content, ['ASCII', 'UTF-8', 'ISO-8859-15']) === 'UTF-8';
    }

    /**
     * Get error message for `testLanguageFilesForInvalidEncoding`.
     *
     * @param string $invalidFilePath  Full path to the file which has wrong encoding
     * @param string $declaredEncoding Declared content encoding within the file
     *
     * @return string Actual error message
     */
    protected function getErrorMessageForTestLanguageFilesForInvalidEncoding($invalidFilePath, $declaredEncoding)
    {
        $invalidFilePath = realpath($invalidFilePath);

        $msg = <<<EOD
UTF-8 characters were detected in "$invalidFilePath" which has "$declaredEncoding" encoding declared.
This could be due to the following reasons:

* The declared encoding within the `charset` key is wrong;
* The file was unintentionally re-encoded as UTF-8;
* The file was intentionally re-encoded as UTF-8 but the declared encoding was not updated.

Assert message:
EOD;

        return $msg;
    }

    /**
     * Look for language files that have undeclared encoding.
     *
     * @param string $languageCode Language code in form of ISO 639-1
     * @param string $type         Language file type
     * @param string $filePattern  File glob pattern to match
     *
     * @dataProvider providerLanguageFilesForInvalidEncoding
     */
    public function testLanguageFilesForUndeclaredEncoding($languageCode, $type, $filePattern)
    {
        $languageFiles = $this->getLangFileContents($type, $languageCode, $filePattern);

        foreach ($languageFiles as $filePath => $fileContent) {
            $languageTranslation = $this->getLanguageFileContents($type, $languageCode, $filePattern);

            $isEncodingDeclared = $languageTranslation['charset'] ? true : false;

            $errorMessage = <<<EOD
Language file "$filePath" has an undeclared `charset` value, this could be due to:

* The charset value being empty;
* The charset value being missing.

Language file with an empty/missing `charset` value is considered as invalid!

Examples of valid `charset` entries:

* 'charset' => 'UTF-8'
* 'charset' => 'ISO-8859-15'

Assert message:
EOD;

            $this->assertTrue($isEncodingDeclared, $errorMessage);
        }
    }

    /**
     * Look for language files that have declared invalid encoding.
     *
     * @param string $languageCode Language code in form of ISO 639-1
     * @param string $type Language file type
     * @param string $filePattern File glob pattern to match
     *
     * @dataProvider providerLanguageFilesForInvalidEncoding
     */
    public function testLanguageFilesForDeclaredInvalidEncoding($languageCode, $type, $filePattern)
    {
        $languageFiles = $this->getLangFileContents($type, $languageCode, $filePattern);

        foreach ($languageFiles as $filePath => $fileContent) {
            $languageTranslation = $this->getLanguageFileContents($type, $languageCode, $filePattern);

            $declaredEncoding = strtolower((string) $languageTranslation['charset']);
            $validEncodings = ['utf-8', 'iso-8859-15'];
            $isValidEncoding = in_array($declaredEncoding, $validEncodings);

            $errorMessage = <<<EOD
Language file "$filePath" has declared an invalid `charset` value: "$declaredEncoding".

Language file with an invalid `charset` value is considered as invalid!

Examples of valid `charset` entries:

* 'charset' => 'UTF-8'
* 'charset' => 'ISO-8859-15'

Assert message:
EOD;

            $this->assertTrue($isValidEncoding, $errorMessage);
        }
    }

    /**
     * dataProvider with a list of all language files for existence integrity check.
     *
     * @return array
     */
    public function providerAllLanguageFilesForExistence()
    {
        $themeName = $this->getThemeName();

        return [
            // LanguageCode, Type, FileName
            ['en', '', 'translit_lang.php'],
            ['en', '', 'lang.php'],
            ['en', $themeName, 'cust_lang.php'],
            ['en', $themeName, 'lang.php'],
            ['en', $themeName, 'map.php'],
            ['en', $themeName, 'theme_options.php'],
            ['en', $this->getAdminThemeName(), 'cust_lang.php.dist'],
            ['en', $this->getAdminThemeName(), 'help_lang.php'],
            ['en', $this->getAdminThemeName(), 'lang.php'],
            ['En', 'Setup', 'lang.php'],

            ['de', '', 'translit_lang.php'],
            ['de', '', 'lang.php'],
            ['de', $themeName, 'cust_lang.php'],
            ['de', $themeName, 'lang.php'],
            ['de', $themeName, 'map.php'],
            ['de', $themeName, 'theme_options.php'],
            ['de', $this->getAdminThemeName(), 'cust_lang.php.dist'],
            ['de', $this->getAdminThemeName(), 'help_lang.php'],
            ['de', $this->getAdminThemeName(), 'lang.php'],
            ['De', 'Setup', 'lang.php'],
        ];
    }

    /**
     * Test expected language files for their existence (OS independent case sensitive checks).
     *
     * @param string $languageCode Language code in form of ISO 639-1
     * @param string $type Language file type
     * @param string $fileName File name of a language file
     *
     * @dataProvider providerAllLanguageFilesForExistence
     */
    public function testAllLanguageFilesForExistence($languageCode, $type, $fileName)
    {
        $filePath = $this->getLanguageFilePath($type, $languageCode, $fileName);
        $isFilePathCorrect = static::file_exists_case_sensitive($filePath);

        $errorMessage = $this->getErrorMessageForTestAllLanguageFilesForExistence($filePath);
        $this->assertTrue($isFilePathCorrect, $errorMessage);
    }

    /**
     * Get error message for `testAllLanguageFilesForExistence`.
     *
     * @param string $missingFilePath Full path to the file which is missing
     *
     * @return string
     */
    protected function getErrorMessageForTestAllLanguageFilesForExistence($missingFilePath)
    {
        $languageFilesProviderData = $this->providerAllLanguageFilesForExistence();

        $filePaths = array_map(function ($providerDataInput) {
            [$languageCode, $type, $fileName] = $providerDataInput;

            return realpath($this->getLanguageFilePath($type, $languageCode, $fileName));
        }, $languageFilesProviderData);

        $filePathsMessage = implode("\n", $filePaths);

        $missingFilePath = realpath($missingFilePath);

        $errorMessage = <<<EOD
The file "$missingFilePath" was not found. This could be due to the following reasons:

* The file was renamed;
* The file was removed;
* One of path elements has it's casing changed, i.e. 'word' => 'Word';
* The theme name (through `getThemeName()`) is wrong.

This issue could also be caused by the behavior of non case-sensitive file system.

If the change of file move/rename was intentional, don't forget to:
* Update current test;
* Double check the integrity test file - 'langIntegrityTest.php';
* Double check the lower and UPPER case differences in path elements.

The whole list of files that are being checked:
$filePathsMessage

Assert message:
EOD;

        return $errorMessage;
    }

    /**
     * Helper function to do an OS independent case sensitive file_exists check.
     *
     * The function uses glob to extract real path for comparing against the given path.
     * It does so by applying an adapted pattern, e.g.
     *     given path: /var/www/oxideshop
     *     pattern: /[Vv]ar/[Ww]ww/[Oo]xideshop
     *
     * Note: Case sensitivity check applies to the whole path, not only the file/directory part, i.e.
     * There's a difference between '/a/b/c' and '/A/b/c' thus it will return different result.
     *
     * TODO: Transfer this to testing library
     *
     * @param string $filePath Given full path to check for case sensitive existence
     *
     * @return bool True in case the whole path matches as case sensitive file_exists, false otherwise
     */
    public static function file_exists_case_sensitive($filePath)
    {
        $filePath = realpath($filePath);

        $pathItems = explode(DIRECTORY_SEPARATOR, $filePath);

        $pathGlobItems = array_map(function ($pathItem) {
            $firstLetter = substr($pathItem, 0, 1);

            if ($firstLetter) {
                // Convert 'word' to '[Ww]ord'
                $begin = '[' . strtoupper($firstLetter) . strtolower($firstLetter) . ']';
                $end = substr($pathItem, 1);
                $result = $begin . $end;
            } else {
                $result = '';
            }

            return $result;
        }, $pathItems);

        $globPath = implode(DIRECTORY_SEPARATOR, $pathGlobItems);
        $searchResultItems = glob($globPath);

        $isPathMatch = ($searchResultItems !== false) && ($searchResultItems[0] === $filePath);

        return $isPathMatch;
    }
}
