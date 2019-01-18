<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
        $this->expectException(\OxidEsales\Eshop\Core\Exception\DatabaseErrorException::class);
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
