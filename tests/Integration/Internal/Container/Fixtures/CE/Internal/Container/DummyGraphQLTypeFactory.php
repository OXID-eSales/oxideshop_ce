<?php declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Fixtures\CE\Internal\Application;

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class DummyGraphQLTypeFactory
{

    /**
     * @var DummyGraphQLType
     */
    private $type = null;

    public function addSubType($type)
    {
        $this->type = $type;
    }

    public function verifySubType()
    {
        return $this->type !== null && $this->type->getInfo() == 'Type is installed';
    }
}
