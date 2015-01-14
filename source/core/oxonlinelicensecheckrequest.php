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
 * Online license check request class used as entity.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
class oxOnlineLicenseCheckRequest extends oxOnlineRequest
{
    /**
     * Web service protocol version.
     *
     * @var string
     */
    public $pVersion = '1.1';

    /**
     * Serial keys.
     *
     * @var string
     */
    public $keys;

    /**
     * Build revision number.
     *
     * @var string
     */
    public $revision;

    /**
     * Product related specific information
     * like amount of sub shops and amount of admin users.
     *
     * @var object
     */
    public $productSpecificInformation;
}
