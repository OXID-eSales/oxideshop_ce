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

class ForwardCompatibleTypeHintWithUnifiedNamespace_3_Test extends \PHPUnit_Framework_TestCase
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
            /** If the function was called successfully, the test would have failed */
            $this->fail(
                'Using instances of concrete classes is not expected to work when functions 
                 use type hints from the unified namespace'
            );
        };
        
        $originalErrorHandler = null;
        try {
            $originalErrorHandler = set_error_handler(
                function ($errno, $errstr, $errfile, $errline) {
                    if (E_RECOVERABLE_ERROR === $errno) {
                        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
                    }

                    return false;
                }
            );
            /**
             * We expect a catchable fatal error here.
             * PHP 5.6 and PHP 7.0 will treat this error differently
             */
            $functionWithTypeHint($object);
        } catch (\ErrorException $exception) {
            /** For PHP 5.6 a custom error handler has been registered, which is capable to catch this error */
        } catch (\TypeError $exception) {
            /** As of PHP 7 a TypeError is thrown */
        } finally {
            // restore original error handler
            set_error_handler($originalErrorHandler);
        }
    }
}
