<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class BackwardsCompatibleCatchingOxExceptionAbsoluteNamespace_7_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Try to catch an \oxException when a given Exception is thrown
     * Creating and instance using \OxidEsales\EshopCommunity\Core\Exception\StandardException::class will return an
     * object, which is not an instance of oxException
     *
     * @throws \Exception $exception
     */
    public function testBackwardsCompatibleCatchingOxExceptionAbsoluteNamespace()
    {
        // $this->markTestSkipped(
        //    'This test will fail on Travis and CI as it MUST run in an own PHP process, which is not possible.'
        // );

        $exception = new \OxidEsales\EshopCommunity\Core\Exception\StandardException();
        try {
            throw $exception;
        } catch (\oxException $exception) {
            /** If the exception got caught, the test has failed */
            $this->fail('The given exception (new \OxidEsales\EshopCommunity\Core\Exception\StandardException()) was caught as \oxException');
        } catch (\Exception $exception) {
            /** If the exception got not caught before, the test has failed */
            $this->assertTrue(true, 'The given exception (new \OxidEsales\EshopCommunity\Core\Exception\StandardException()) was not caught as \oxException');
        }
    }
}
