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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

/**
 * Class StateTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Application\Model
 */
class StateTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Entries in the table oxstates are unique by a composite key of the columns `OXID` and `OXCOUNTRYID`.
     *
     * See https://bugs.oxid-esales.com/view.php?id=5029
     */
    public function testInsertingDuplicateStatesIsNotPossible()
    {
        $this->setExpectedException(\OxidEsales\Eshop\Core\Exception\DatabaseErrorException::class);
        $database = $this->getDb();
        $sql = "INSERT INTO `oxstates` (`OXID`, `OXCOUNTRYID`) VALUES (?, ?)";
        $database->execute($sql, ['duplicateOxid', 'duplicateCountryId']);
        $database->execute($sql, ['duplicateOxid', 'duplicateCountryId']);
    }

    /**
     * Entries in the table oxstates are unique by a composite key of the columns `OXID` and `OXCOUNTRYID`.
     *
     * See https://bugs.oxid-esales.com/view.php?id=5029
     */
    public function testInsertingDuplicateOxidButDifferentCountryIdIsPossible()
    {
        $database = $this->getDb();
        $sql = "INSERT INTO `oxstates` (`OXID`, `OXCOUNTRYID`) VALUES (?, ?)";
        try {
            $database->execute($sql, ['duplicateOxid', 'CountryId-1']);
            $database->execute($sql, ['duplicateOxid', 'CountryId-2']);
        } catch (\OxidEsales\Eshop\Core\Exception\DatabaseErrorException $exception) {
            $this->fail("Inserting two states with duplicate OXIDs but different countryIds is not possible");
        }
    }
}
