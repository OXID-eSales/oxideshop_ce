<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleCatchingUnifiedNamespaceStandardException_2_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Try to catch an \oxException when a given Exception is thrown
     *
     * @throws \Exception $exception
     */
    public function testForwardCompatibleCatchingUnifiedNamespaceStandardException()
    {
        $exception = oxNew('oxException');
        try {
            throw $exception;
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            /** If the exception has been caught, the test has passed */
            $this->assertTrue(true, 'The given exception (oxNew(\'oxException\')) was caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        } catch (\Exception $exception) {
            /** If the exception has not been caught before, the test has failed */
            $this->fail('The given exception (oxNew(\'oxException\')) was not caught as \OxidEsales\Eshop\Core\Exception\StandardException');
        }
    }
}
