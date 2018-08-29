<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\FormConfiguration;

use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FormConfiguration;

class FromConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFieldConfiguration()
    {
        $fieldConfiguration = new FieldConfiguration();
        $fieldConfiguration->setName('testField');

        $formConfiguration = new FormConfiguration();
        $formConfiguration->addFieldConfiguration($fieldConfiguration);

        $this->assertSame(
            [$fieldConfiguration],
            $formConfiguration->getFieldConfigurations()
        );
    }
}
