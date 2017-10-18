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
        return array(
            array('testConfigValue', true),
            array('<script>alert("test script");</script>', false),
            array('<script parameters>alert("test script");</script>', false),
            array('<script src="/assets/javascripts/application.js"></script>', false),
        );
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
