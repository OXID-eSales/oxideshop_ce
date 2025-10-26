<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use PHPUnit\Framework\TestCase;

final class MediaPathTest extends TestCase
{
    public function testConstructWithEmptyPathWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MediaPath('');
    }

    public function testConstructWithInvalidCharactersWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MediaPath('invalid:path.jpg');
    }

    public function testConstructWithAbsolutePathWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MediaPath('/absolute/path.jpg');
    }
}
