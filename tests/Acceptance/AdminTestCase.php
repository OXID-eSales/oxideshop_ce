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

abstract class AdminTestCase extends AcceptanceTestCase
{

    /**
     * Assert that the given text is in the table at the wished position.
     *
     * @param string $expectedText The text, that should be at the given position.
     * @param int    $row          The table row, in which the expected text should be.
     * @param int    $column       The table column, in which the expected text should be.
     */
    protected function assertTextExistsAtTablePosition($expectedText, $row, $column)
    {
        $this->assertEquals($expectedText, $this->getText("//tr[@id='row.${row}']/td[${column}]"));
    }

}
