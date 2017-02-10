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
namespace OxidEsales\EshopCommunity\Tests\Integration\Multilanguage;

use oxDb;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Application\Model\ShopViewValidator;
use OxidEsales\Eshop\Core\DbMetaDataHandler;

/**
 * Regression tests for Shop class and ShopViewValidator class.
 */
class EscapeDropViewTest extends MultilanguageTestCase
{
    /**
     * Testing Shop::_cleanInvalidViews().
     */
    public function testEscapeDropView()
    {
        $this->createView();

        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS  WHERE TABLE_NAME LIKE 'oxv_oxcountry_xx-2015'";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sql);
        $this->assertSame('oxv_oxcountry_xx-2015', $result);

        $shop = oxNew(Shop::class);
        $shop->generateViews();

        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS  WHERE TABLE_NAME LIKE 'oxv_oxcountry_xx-2015'";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sql);
        $this->assertSame(false, $result);
    }

    /**
     * Testing ShopViewValidator::_getAllViews().
     */
    public function testShowViewTables()
    {
        $shopViewValidator = oxNew(ShopViewValidator::class);

        $invalidViews = $shopViewValidator->getInvalidViews();

        $this->assertNotEmpty($invalidViews);
        $this->assertNotContains('oxvouchers', $invalidViews);
    }

    /**
     * Create additional view.
     *
     * @param string $table
     */
    protected function createView($table = 'oxcountry')
    {
        $langAddition = '_xx-2015';
        $queryStart = 'CREATE OR REPLACE SQL SECURITY INVOKER VIEW';
        $viewTable = "oxv_{$table}{$langAddition}";

        $fields = $this->getViewSelectMultilang($table);
        $join = $this->getViewJoinAll($table);

        $sql = "{$queryStart} `{$viewTable}` AS SELECT {$fields} FROM {$table}{$join}";

        oxDb::getDb()->execute($sql);
    }

    /**
     * Returns all language table view JOIN section.
     *
     * @param string $table table name
     *
     * @return string
     */
    protected function getViewJoinAll($table)
    {
        $join = ' ';
        $metaData = oxNew(DbMetaDataHandler::class);
        $tables = $metaData->getAllMultiTables($table);
        if (count($tables)) {
            foreach ($tables as $tableKey => $tableName) {
                $join .= "LEFT JOIN {$tableName} USING (OXID) ";
            }
        }

        return $join;
    }

    /**
     * Returns table fields sql section for multiple language views.
     *
     * @param string $table table name
     *
     * @return string
     */
    protected function getViewSelectMultilang($table)
    {
        $fields = array();

        $metaData = oxNew(DbMetaDataHandler::class);
        $tables = array_merge(array($table), $metaData->getAllMultiTables($table));
        foreach ($tables as $tableKey => $tableName) {
            $tableFields = $metaData->getFields($tableName);
            foreach ($tableFields as $coreField => $field) {
                if (!isset($fields[$coreField])) {
                    $fields[$coreField] = $field;
                }
            }
        }

        return implode(',', $fields);
    }

}
