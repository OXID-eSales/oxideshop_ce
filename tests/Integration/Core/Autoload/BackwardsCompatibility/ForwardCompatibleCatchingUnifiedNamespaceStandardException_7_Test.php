<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleCatchingUnifiedNamespaceStandardException_7_Test extends \PHPUnit\Framework\TestCase
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

        $exception = new \OxidEsales\EshopCommunity\Core\Exception\StandardException();
        try {
            throw $exception;
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            /** If the exception has been caught, the test has failed */
            $this->fail('The given exception (new \OxidEsales\Eshop\Core\Exception\StandardException()) was caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        } catch (\Exception $exception) {
            /** If the exception has not been caught before, the test has failed */
            $this->assertTrue(true, 'The given exception (new \OxidEsales\Eshop\Core\Exception\StandardException()) was not caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        }
    }
}
