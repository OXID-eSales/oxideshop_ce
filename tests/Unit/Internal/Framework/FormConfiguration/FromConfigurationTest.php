<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\FormConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FieldConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FormConfiguration;

class FromConfigurationTest extends \PHPUnit\Framework\TestCase
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
