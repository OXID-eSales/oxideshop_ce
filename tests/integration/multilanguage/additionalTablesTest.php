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

require_once 'MultilanguageTestCase.php';

class Integration_Multilanguage_AdditionalTablesTest extends MultilanguageTestCase
{
    /**
     * Additional multilanguage tables.
     *
     * @var array
     */
    protected $_aAdditionalTables = array();

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

    }

    /**
     * Fixture tearDown.
     */
    protected function tearDown()
    {
        $this->setConfigParam('aMultiLangTables', array());
        $this->_updateViews();

        foreach ($this->_aAdditionalTables as $sName) {
            $this->_removeAdditionalTables($sName);
        }
        $this->_removeAdditionalTables('set1');

        parent::tearDown();
    }

    /**
     * Assert that set tables are automatically created for additional multilanguage table
     * in case we add first the table and then create the languages.
     */
    public function testCreateLanguagesAfterAdditionalTable()
    {
        $this->_createTable('addtest');
        $this->setConfigParam('aMultiLangTables', array('addtest'));

        //add nine more languages
        $this->_prepare(9);

        $sSql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME LIKE 'addtest_set1'";
        $sResult = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sSql);
        $this->assertEquals('addtest_set1', $sResult);
    }

    /**
     * Assert that set tables are automatically created for additional multilanguage table
     * in case first create the languages, then set the table in config.inc.php variable 'aMultiLangTables'
     * and call updateViews. Without *_set1 tables, view creating throws and exception.
     */
    public function testCreateAdditionalTableAfterCreatingLanguages()
    {
        //add nine more languages
        $this->_prepare(9);

        $this->_createTable('addtest');
        $this->setConfigParam('aMultiLangTables', array('addtest'));

        $this->_updateViews();

        $sSql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME LIKE 'addtest_set1'";
        $sResult = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sSql);
        $this->assertEquals('addtest_set1', $sResult);

    }

    /**
     * Verify that the expected data turned up in the language views
     */
    public function testViewContentsCreateLanguagesAfterAdditionalTable()
    {
        $sOXID = '_test101';

        $this->_createTable('addtest');
        $this->setConfigParam('aMultiLangTables', array('addtest'));

        //add nine more languages
        $iLanguageId = $this->_prepare(9);

        //insert testdata for language id 0
        $sSql = "INSERT INTO addtest (OXID, TITLE) VALUES ('" . $sOXID . "', 'some default title')";
        oxDb::getDb()->query($sSql);

        //insert testdata for last added language id in set1 table
        $sSql = "INSERT INTO addtest_set1 (OXID, TITLE_" . $iLanguageId . ") VALUES ('" . $sOXID . "', 'some additional title')";
        oxDb::getDb()->query($sSql);

        $sSql = "SELECT TITLE FROM " . getViewName('addtest', $iLanguageId) . " WHERE OXID = '" . $sOXID . "'";
        $this->assertSame('some additional title', oxDb::getDb()->getOne($sSql));

        $sSql = "SELECT TITLE FROM " . getViewName('addtest', 0) . " WHERE OXID = '" . $sOXID . "'";
        $this->assertSame('some default title', oxDb::getDb()->getOne($sSql));

    }

    /**
     * Verify that the expected data turned up in the language views
     */
    public function testViewContentsCreateAdditionalTableAfterCreatingLanguages()
    {
        //add nine more languages
        $iLanguageId = $this->_prepare(9);
        $sOXID = '_test101';

        $this->_createTable('addtest');
        $this->setConfigParam('aMultiLangTables', array('addtest'));

        $this->_updateViews();

        //insert testdata for language id 0
        $sSql = "INSERT INTO addtest (OXID, TITLE) VALUES ('" . $sOXID . "', 'some default title')";
        oxDb::getDb()->query($sSql);

        //insert testdata for last added language id in set1 table
        $sSql = "INSERT INTO addtest_set1 (OXID, TITLE_" . $iLanguageId . ") VALUES ('" . $sOXID . "', 'some additional title')";
        oxDb::getDb()->query($sSql);

        $sSql = "SELECT TITLE FROM " . getViewName('addtest', $iLanguageId) . " WHERE OXID = '" . $sOXID . "'";
        $this->assertSame('some additional title', oxDb::getDb()->getOne($sSql));

        $sSql = "SELECT TITLE FROM " . getViewName('addtest', 0) . " WHERE OXID = '" . $sOXID . "'";
        $this->assertSame('some default title', oxDb::getDb()->getOne($sSql));

    }

    /**
     * Create additional multilanguage table.
     *
     * @param string $name
     */
    protected function _createTable($name = 'addtest')
    {
        $sSql = "CREATE TABLE `" . $name . "` (" .
                "`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Item id'," .
                "`TITLE` varchar(128) NOT NULL DEFAULT '' COMMENT 'Title (multilanguage)'," .
                "`TITLE_1` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_2` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_3` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_4` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_5` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_6` varchar(128) NOT NULL DEFAULT ''," .
                "`TITLE_7` varchar(128) NOT NULL DEFAULT ''," .
                "PRIMARY KEY (`OXID`)" .
                ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for testing'";

        oxDb::getDb()->query($sSql);
        oxDb::getInstance()->getTableDescription($name); //throws exception if table does not exist
        $this->_aAdditionalTables[] = $name;
    }

    /**
     * Remove additional multilanguage tables and related.
     *
     * @return null
     */
    protected function _removeAdditionalTables($sName)
    {
        $sSql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES  WHERE TABLE_NAME LIKE '%" . $sName . "%'";
        $aRes = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($sSql);
        foreach ($aRes as $aSub) {
            oxDb::getDb()->query("DROP TABLE IF EXISTS `" . $aSub['TABLE_NAME'] . "`");
        }
    }

}

