<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use PHPUnit\Framework\TestCase;

final class MediaAttributeServiceTest extends TestCase
{
    use ContainerTrait;
    use DatabaseTrait;

    private MediaAttributeServiceInterface $service;
    private MediaAttributeDaoInterface $dao;
    private Media $media;
    private Locale $locale;
    private int $shopId;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction($this->get(ConnectionFactoryInterface::class)->create());
        $this->service = $this->get(MediaAttributeServiceInterface::class);
        $this->dao = $this->get(MediaAttributeDaoInterface::class);
        $this->locale = $this->get(ActiveLocaleProviderInterface::class)->getActiveLocale();
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->media = $this->createMedia();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction($this->get(ConnectionFactoryInterface::class)->create());
        parent::tearDown();
    }

    public function testGetReturnsAllAttributes(): void
    {
        $this->dao->save($this->createAttribute($this->media, $this->locale, MediaAttribute::ALT, 'alt value'));
        $this->dao->save($this->createAttribute($this->media, $this->locale, 'title', 'title value'));

        $result = $this->service->getAttributes($this->media, $this->locale->getCode());

        $this->assertTrue($result->has(MediaAttribute::ALT));
        $this->assertTrue($result->has('title'));
    }

    public function testGetReturnsEmptyAttributesWhenNoneExist(): void
    {
        $result = $this->service->getAttributes($this->media, $this->locale->getCode());

        $this->assertFalse($result->has(MediaAttribute::ALT));
    }

    public function testSave(): void
    {
        $this->service->save(MediaAttribute::ALT, 'some alt text', $this->media, $this->locale->getCode());

        $this->assertSame(
            'some alt text',
            $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId)->getAlt()
        );
    }

    public function testSaveWithGenericName(): void
    {
        $this->service->save('title', 'some title', $this->media, $this->locale->getCode());

        $media = $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId);

        $this->assertSame('some title', $media->get('title'));
    }

    public function testSaveUpdatesExistingValue(): void
    {
        $this->service->save(MediaAttribute::ALT, 'original', $this->media, $this->locale->getCode());
        $this->service->save(MediaAttribute::ALT, 'updated', $this->media, $this->locale->getCode());

        $this->assertSame(
            'updated',
            $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId)->getAlt()
        );
    }

    public function testDelete(): void
    {
        $this->service->save(MediaAttribute::ALT, 'some alt text', $this->media, $this->locale->getCode());

        $this->service->delete(MediaAttribute::ALT, $this->media, $this->locale->getCode());

        $this->assertFalse(
            $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId)->has(MediaAttribute::ALT)
        );
    }

    public function testSaveKeepsGivenValue(): void
    {
        $this->service->save(MediaAttribute::ALT, '  stored  ', $this->media, $this->locale->getCode());

        $this->assertSame(
            '  stored  ',
            $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId)->getAlt()
        );
    }

    public function testDeleteDoesNotRemoveOtherAttributes(): void
    {
        $this->service->save(MediaAttribute::ALT, 'some alt text', $this->media, $this->locale->getCode());
        $this->service->save('title', 'some title', $this->media, $this->locale->getCode());

        $this->service->delete(MediaAttribute::ALT, $this->media, $this->locale->getCode());

        $this->assertSame(
            'some title',
            $this->dao->getAttributes($this->media->getId(), $this->locale->getCode(), $this->shopId)->get('title')
        );
    }

    private function createMedia(): Media
    {
        $media = new Media(Id::generate(), new MediaPath('media/test.jpg'), new MediaType('image/jpeg'));
        $this->get(MediaDaoInterface::class)->add($media);
        return $media;
    }

    private function createAttribute(Media $media, Locale $locale, string $name, string $value): MediaAttribute
    {
        return new MediaAttribute(
            Id::generate(),
            $media->getId(),
            $locale->getCode(),
            $this->shopId,
            $name,
            $value
        );
    }
}
