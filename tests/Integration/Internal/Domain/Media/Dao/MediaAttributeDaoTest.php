<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Media\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\LocaleChain;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class MediaAttributeDaoTest extends IntegrationTestCase
{
    private MediaAttributeDaoInterface $dao;
    private MediaDaoInterface $mediaDao;

    public function setUp(): void
    {
        parent::setUp();
        $this->dao = $this->get(MediaAttributeDaoInterface::class);
        $this->mediaDao = $this->get(MediaDaoInterface::class);
    }

    public function testSaveAndGet(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'some alt text'));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1);

        $this->assertTrue($result->has(MediaAttribute::ALT));
        $this->assertSame('some alt text', $result->get(MediaAttribute::ALT));
    }

    public function testSaveUpdatesExistingEntry(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'original'));

        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'updated value'));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1);
        $this->assertSame('updated value', $result->get(MediaAttribute::ALT));
    }

    public function testGetReturnsAllAttributesForMedia(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'alt value'));
        $this->dao->save($this->createAttribute($mediaId, 'title', 'title value'));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1);

        $this->assertTrue($result->has(MediaAttribute::ALT));
        $this->assertTrue($result->has('title'));
        $this->assertSame('alt value', $result->get(MediaAttribute::ALT));
        $this->assertSame('title value', $result->get('title'));
    }

    public function testGetReturnsEmptyAttributesWhenNoneExist(): void
    {
        $result = $this->dao->getAttributes($this->createMedia(), new LocaleChain(['de_DE']), 1);

        $this->assertFalse($result->has(MediaAttribute::ALT));
    }

    public function testGetIgnoresOtherLocales(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'german'));
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'english', 'en_GB'));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1);

        $this->assertSame('german', $result->get(MediaAttribute::ALT));
    }

    public function testGetIgnoresOtherShops(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'shop 1'));
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'shop 2', 'de_DE', 2));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1);

        $this->assertSame('shop 1', $result->get(MediaAttribute::ALT));
    }

    public function testGetByChainUsesFirstAvailableAttributeInLocaleOrder(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, 'title', 'english title', 'en_GB'));
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'german alt', 'de_DE'));
        $this->dao->save($this->createAttribute($mediaId, 'title', 'german title', 'de_DE'));

        $result = $this->dao->getAttributes(
            $mediaId,
            new LocaleChain(['fr_FR', 'en_GB', 'de_DE']),
            1
        );

        $this->assertSame('english title', $result->get('title'));
        $this->assertSame('german alt', $result->getAlt());
    }

    public function testGetByChainIgnoresOtherShops(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'shop 2 alt', 'en_GB', 2));
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'shop 1 alt', 'de_DE', 1));

        $result = $this->dao->getAttributes(
            $mediaId,
            new LocaleChain(['en_GB', 'de_DE']),
            1
        );

        $this->assertSame('shop 1 alt', $result->getAlt());
    }

    public function testGetByEmptyChainReturnsEmptyAttributes(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'german alt', 'de_DE'));

        $result = $this->dao->getAttributes($mediaId, new LocaleChain([]), 1);

        $this->assertFalse($result->has(MediaAttribute::ALT));
    }

    public function testDelete(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'some alt text'));

        $this->dao->delete(MediaAttribute::ALT, $mediaId, 'de_DE', 1);

        $this->assertFalse(
            $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1)->has(MediaAttribute::ALT)
        );
    }

    public function testDeleteOnlyRemovesSpecifiedLocale(): void
    {
        $mediaId = $this->createMedia();
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'german', 'de_DE'));
        $this->dao->save($this->createAttribute($mediaId, MediaAttribute::ALT, 'english', 'en_GB'));

        $this->dao->delete(MediaAttribute::ALT, $mediaId, 'de_DE', 1);

        $this->assertFalse(
            $this->dao->getAttributes($mediaId, new LocaleChain(['de_DE']), 1)->has(MediaAttribute::ALT)
        );
        $this->assertSame(
            'english',
            $this->dao->getAttributes($mediaId, new LocaleChain(['en_GB']), 1)->get(MediaAttribute::ALT)
        );
    }

    private function createMedia(): Id
    {
        $mediaId = Id::generate();
        $this->mediaDao->add(new Media($mediaId, new MediaPath('media/test.jpg'), new MediaType('image/jpeg')));
        return $mediaId;
    }

    private function createAttribute(
        Id $mediaId,
        string $name,
        string $value,
        string $localeCode = 'de_DE',
        int $shopId = 1
    ): MediaAttribute {
        return new MediaAttribute(Id::generate(), $mediaId, $localeCode, $shopId, $name, $value);
    }
}
