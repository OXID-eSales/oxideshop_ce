<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class MediaDaoTest extends TestCase
{
    use DatabaseTrait;
    use ContainerTrait;

    private MediaDaoInterface $mediaDao;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
        $this->mediaDao = $this->get(MediaDaoInterface::class);
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());
        parent::tearDown();
    }

    public function testCreateAndGetMedia(): void
    {
        $path = 'media/test_mdt.png';
        $type = 'image/png';

        $createdMedia = $this->mediaDao->create(path: $path, type: $type);

        $this->assertInstanceOf(Media::class, $createdMedia);
        $this->assertNotEmpty($createdMedia->getId());
        $this->assertSame($path, $createdMedia->getPath());
        $this->assertSame($type, $createdMedia->getType());

        $retrievedMedia = $this->mediaDao->get($createdMedia->getId());

        $this->assertEquals($createdMedia, $retrievedMedia);
    }

    public function testGetMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->mediaDao->get('nonexistent_media_id');
    }

    public function testDeleteMedia(): void
    {
        $createdMedia = $this->mediaDao->create(path: 'to/be/deleted.jpg', type: 'image/jpeg');
        $mediaId = $createdMedia->getId();

        $this->mediaDao->delete($mediaId);

        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->mediaDao->get($mediaId);
    }

    public function testDeleteMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);
        $this->mediaDao->delete('nonexistent_media_id_for_delete');
    }
}
