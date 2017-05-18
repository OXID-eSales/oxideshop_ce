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

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleTypeHintWithUnifiedNamespaceNamespace_3_Test extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the backwards compatibility with camel cased type hints
     */
    public function testForwardCompatibleTypeHintWithUnifiedNamespaceNamespace()
    {
        $this->markTestSkipped('Bc type hints do not work on instances of concrete classes');

        $object = oxNew(\OxidEsales\EshopCommunity\Application\Model\Article::class);
        /**
         * @param \OxidEsales\Eshop\Application\Model\Article $object
         */
        $functionWithTypeHint = function (\OxidEsales\Eshop\Application\Model\Article $object) {
            /** If the function was called successfully, the test would have passed */
            $this->assertTrue(true);
        };
        /** The function call would produce a catchable fatal error, if the type hint is not correct */
        $functionWithTypeHint($object);
    }
}
