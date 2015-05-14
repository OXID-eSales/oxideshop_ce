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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * SEPA (Single Euro Payments Area) BIC validation class
 *
 */
class oxSepaBICValidator
{

    /**
     * Business identifier code validation
     *
     * Structure
     *  - 4 letters: Institution Code or bank code.
     *  - 2 letters: ISO 3166-1 alpha-2 country code
     *  - 2 letters or digits: location code
     *  - 3 letters or digits: branch code, optional
     *
     * @param string $sBIC code to check
     *
     * @return bool
     */
    public function isValid($sBIC)
    {
        $sBIC = strtoupper(trim($sBIC));

        return (bool) getStr()->preg_match("(^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$)", $sBIC);
    }
}
