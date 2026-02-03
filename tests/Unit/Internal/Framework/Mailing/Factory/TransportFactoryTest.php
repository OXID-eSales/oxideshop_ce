<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Mailing\Factory;

use OxidEsales\EshopCommunity\Internal\Framework\Mailing\Factory\TransportFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Exception\InvalidArgumentException;
use Symfony\Component\Mailer\Transport\TransportInterface;

class TransportFactoryTest extends TestCase
{
    public function testCreateReturnsTransportInterface(): void
    {
        $factory = new TransportFactory('null://null');

        $this->assertInstanceOf(TransportInterface::class, $factory->create());
    }

    public function testCreateThrowsExceptionOnInvalidDsn(): void
    {
        $factory = new TransportFactory('invalid-dsn-without-scheme');

        $this->expectException(InvalidArgumentException::class);
        $factory->create();
    }

    public function testCreateThrowsExceptionOnEmptyDsn(): void
    {
        $factory = new TransportFactory('');

        $this->expectException(InvalidArgumentException::class);
        $factory->create();
    }
}
