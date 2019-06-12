<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleCatchingUnifiedNamespaceStandardException_1_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Try to catch an \oxException when a given Exception is thrown
     *
     * @throws \Exception $exception
     */
    public function testForwardCompatibleCatchingUnifiedNamespaceStandardException()
    {
        // $this->markTestSkipped(
        //    'This test will fail on Travis and CI as it MUST run in an own PHP process, which is not possible.'
        // );

        $exception = oxNew(\OxidEsales\EshopCommunity\Core\Exception\StandardException::class);
        try {
            throw $exception;
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            /** If the exception has been caught, the test has failed */
            $this->fail('The given exception (oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class)) was caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        } catch (\Exception $exception) {
            /** If the exception has not been caught before, the test has passed */
            $this->assertTrue(true, 'The given exception (oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class)) was not caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        }
    }
}
