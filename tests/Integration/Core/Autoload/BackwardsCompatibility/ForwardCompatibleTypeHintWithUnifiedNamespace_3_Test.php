<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleTypeHintWithUnifiedNamespace_3_Test extends \PHPUnit\Framework\TestCase
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
