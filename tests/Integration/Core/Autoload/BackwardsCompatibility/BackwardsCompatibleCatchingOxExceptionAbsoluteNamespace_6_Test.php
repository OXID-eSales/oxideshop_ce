<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class BackwardsCompatibleCatchingOxExceptionAbsoluteNamespace_6_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Try to catch an \oxException when a given Exception is thrown
     *
     * @throws \Exception $exception
     */
    public function testBackwardsCompatibleCatchingOxExceptionAbsoluteNamespace()
    {
        $exception = new \oxException();
        try {
            throw $exception;
        } catch (\oxException $exception) {
            /** If the exception got caught, the test has passed */
            $this->assertTrue(true, 'The given exception (new \oxException()) was caught as \oxException');
        } catch (\Exception $exception) {
            /** If the exception got not caught before, the test has failed */
            $this->fail('The given exception (new \oxException()) was not caught as \oxException');
        }
    }
}
