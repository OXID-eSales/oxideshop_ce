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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\TestSqlPathProvider;

abstract class AcceptanceTestCase extends \OxidEsales\TestingLibrary\AcceptanceTestCase
{
    /**
     * Adds tests sql data to database.
     *
     * @param string $sTestSuitePath
     */
    public function addTestData($sTestSuitePath)
    {
        parent::addTestData($sTestSuitePath);

        $editionSelector = new EditionSelector();

        if ($editionSelector->isEnterprise()) {
            $testSqlPathProvider = new TestSqlPathProvider(new EditionSelector(), $this->getTestConfig()->getShopPath());
            $sTestSuitePath = realpath($testSqlPathProvider->getDataPathBySuitePath($sTestSuitePath));

            $sFileName = $sTestSuitePath . '/demodata_' . SHOP_EDITION . '.sql';
            if (file_exists($sFileName)) {
                $this->importSql($sFileName);
            }

            if (isSUBSHOP && file_exists($sTestSuitePath . '/demodata_EE_mall.sql')) {
                $this->importSql($sTestSuitePath . '/demodata_EE_mall.sql');
            }
        }
    }
}
