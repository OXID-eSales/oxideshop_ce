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
 * @copyright (C) OXID eSales AG 2003-2018
 * @version   OXID eShop CE
 */

require_once 'AllTestsRunner.php';

/**
 * PHPUnit_Framework_TestCase implementation for adding and testing all selenium tests from this dir
 */
class AllTestsForTravis extends AllTestsRunner
{
    /** @var array Default test suites */
    protected static $_aTestSuites = array(
        'unit/admin',
        'unit/core',
        'unit/maintenance',
        'unit/setup',
        'unit/views',
        'integration/admin',
        'integration/article',
        'integration/checkout',
        'integration/encryptor',
        'integration/models',
        'integration/modules',
        'integration/multilanguage',
        'integration/onlineinfo',
        'integration/price',
        'integration/seo',
        'integration/restrictedAddress',
        'integration/timestamp',
        'integration/user',
        'unit/components',
        'unit/modules',
    );
}
