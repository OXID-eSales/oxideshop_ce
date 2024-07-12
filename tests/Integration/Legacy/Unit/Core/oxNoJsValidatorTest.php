<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class oxNoJsValidatorTest extends \OxidTestCase
{
    /**
     * @return array
     */
    public function providerValidatesForJavaScript()
    {
        return [['testConfigValue', true], ['<script>alert("test script");</script>', false], ['<script parameters>alert("test script");</script>', false], ['<script src="/assets/javascripts/application.js"></script>', false]];
    }

    /**
     * @param string $configValue
     * @param bool   $isValid
     *
     * @dataProvider providerValidatesForJavaScript
     */
    public function testValidatesForJavaScript($configValue, $isValid)
    {
        $configValidator = oxNew('oxNoJsValidator');

        $this->assertSame($isValid, $configValidator->isValid($configValue));
    }
}
