<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
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
        $mediaId = Id::generate();
        $mediaPath = new MediaPath('media/test_mdt.png');
        $mediaType = new MediaType('image/png');
        $media = new Media(
            $mediaId,
            $mediaPath,
            $mediaType
        );

        $this->mediaDao->add($media);
        $fetched = $this->mediaDao->get($mediaId);

        $this->assertEquals($media, $fetched);
    }

    public function testGetMediaThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->mediaDao->get(Id::generate());
    }

    public function testDeleteMedia(): void
    {
        $mediaId = Id::generate();
        $mediaPath = new MediaPath('media/test_mdt.png');
        $mediaType = new MediaType('image/png');
        $media = new Media(
            $mediaId,
            $mediaPath,
            $mediaType
        );
        $this->mediaDao->add($media);

        $this->mediaDao->delete($mediaId);

        $this->expectException(EntryDoesNotExistDaoException::class);

        $this->mediaDao->get($mediaId);
    }
}
