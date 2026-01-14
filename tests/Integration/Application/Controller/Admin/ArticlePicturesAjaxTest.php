<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ArticlePicturesAjax;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUploaderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;

final class ArticlePicturesAjaxTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const VALID_IMAGE = 'valid_image.jpg';
    private const FIXTURES_PATH = '/Fixtures/';

    public function testRemoveMedia(): void
    {
        $productMediaId = Id::generate();
        $productId = Id::generate();

        $this->setupContainerWithRequest([
            'productMediaId' => (string) $productMediaId,
            'role' => ProductMediaRole::DETAIL,
        ]);

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($productMediaId, $productId, ProductMediaRole::DETAIL);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->removeMedia();

        $this->assertCount(0, $this->get(ProductMediaDaoInterface::class)->getAll($productId));
    }

    public function testRemoveMediaRemovesOnlyRoleWhenMultipleRolesExist(): void
    {
        $productMediaId = Id::generate();
        $productId = Id::generate();

        $this->setupContainerWithRequest([
            'productMediaId' => (string) $productMediaId,
            'role' => ProductMediaRole::ICON,
        ]);

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($productMediaId, $productId, ProductMediaRole::ICON, ProductMediaRole::THUMBNAIL);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->removeMedia();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);
        $this->assertCount(1, $allMedia);

        $existingMedia = $this->get(ProductMediaDaoInterface::class)->get($productMediaId);
        $this->assertFalse($existingMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($existingMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
    }

    public function testToggleMediaActiveStateDeactivates(): void
    {
        $productMediaId = Id::generate();
        $productId = Id::generate();

        $this->setupContainerWithRequest([
            'productMediaId' => (string) $productMediaId,
        ]);

        $this->createArticleWithId((string) $productId);
        $productMedia = $this->addProductMediaWithId($productMediaId, $productId, ProductMediaRole::DETAIL);

        $this->assertTrue($productMedia->isActive());

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->toggleMediaActiveState();

        $updatedMedia = $this->get(ProductMediaDaoInterface::class)->get($productMediaId);
        $this->assertFalse($updatedMedia->isActive());
    }

    public function testToggleMediaActiveStateActivates(): void
    {
        $productMediaId = Id::generate();
        $productId = Id::generate();

        $this->setupContainerWithRequest([
            'productMediaId' => (string) $productMediaId,
        ]);

        $this->createArticleWithId((string) $productId);
        $productMedia = $this->addProductMediaWithId($productMediaId, $productId, ProductMediaRole::DETAIL);
        $this->get(ProductMediaServiceInterface::class)->deactivate($productMedia);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->toggleMediaActiveState();

        $updatedMedia = $this->get(ProductMediaDaoInterface::class)->get($productMediaId);
        $this->assertTrue($updatedMedia->isActive());
    }

    public function testSortMedia(): void
    {
        $productId = Id::generate();
        $mediaId1 = Id::generate();
        $mediaId2 = Id::generate();
        $mediaId3 = Id::generate();

        $newOrder = [
            (string) $mediaId3,
            (string) $mediaId1,
            (string) $mediaId2,
        ];

        $this->setupContainerWithRequest([
            'sorting' => json_encode($newOrder),
        ]);

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($mediaId1, $productId, ProductMediaRole::DETAIL);
        $this->addProductMediaWithId($mediaId2, $productId, ProductMediaRole::DETAIL);
        $this->addProductMediaWithId($mediaId3, $productId, ProductMediaRole::DETAIL);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->sortMedia();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);

        $positions = [];
        foreach ($allMedia as $m) {
            $positions[(string) $m->getId()] = $m->getPosition();
        }

        $this->assertLessThan($positions[(string) $mediaId1], $positions[(string) $mediaId3]);
        $this->assertLessThan($positions[(string) $mediaId2], $positions[(string) $mediaId1]);
    }

    public function testAddMedia(): void
    {
        $fixture = Path::join(__DIR__, self::FIXTURES_PATH, self::VALID_IMAGE);

        $uploadedFile = new UploadedFile(
            $fixture,
            self::VALID_IMAGE,
            'image/jpeg',
            null,
            true
        );

        $productId = Id::generate();

        $this->setupContainerWithRequestAndMocks(
            [
                'productId' => (string) $productId,
                'role' => ProductMediaRole::DETAIL,
            ],
            [$uploadedFile]
        );

        $this->createArticleWithId((string) $productId);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->addMedia();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);
        $this->assertCount(1, $allMedia);
        $this->assertTrue($allMedia->first()->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::DETAIL)));
    }

    public function testReplaceMedia(): void
    {
        $fixture = Path::join(__DIR__, self::FIXTURES_PATH, self::VALID_IMAGE);

        $uploadedFile = new UploadedFile(
            $fixture,
            self::VALID_IMAGE,
            'image/jpeg',
            null,
            true
        );

        $productId = Id::generate();
        $existingMediaId = Id::generate();

        $this->setupContainerWithRequestAndMocks(
            [
                'productId' => (string) $productId,
                'role' => ProductMediaRole::ICON,
                'productMediaId' => (string) $existingMediaId,
            ],
            [$uploadedFile]
        );

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($existingMediaId, $productId, ProductMediaRole::ICON);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->replaceMedia();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);
        $this->assertCount(1, $allMedia);
        $this->assertNotEquals((string) $existingMediaId, (string) $allMedia->first()->getId());
    }

    public function testReplaceMediaKeepsExistingWhenValidationFails(): void
    {
        $fixture = Path::join(__DIR__, self::FIXTURES_PATH, self::VALID_IMAGE);
        $uploadedFile = new UploadedFile($fixture, self::VALID_IMAGE, 'image/jpeg', null, true);
        $productId = Id::generate();
        $existingMediaId = Id::generate();

        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '1048576',
            ]
        ]);

        $this->setupContainerWithRequest(
            [
                'productId' => (string) $productId,
                'role' => ProductMediaRole::ICON,
                'productMediaId' => (string) $existingMediaId,
            ],
            [$uploadedFile]
        );

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($existingMediaId, $productId, ProductMediaRole::ICON);

        ob_start();
        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->replaceMedia();
        ob_end_clean();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);
        $this->assertEquals((string) $existingMediaId, (string) $allMedia->first()->getId());
    }

    public function testReplaceMediaRemovesOnlyRoleWhenMultipleRolesExist(): void
    {
        $fixture = Path::join(__DIR__, self::FIXTURES_PATH, self::VALID_IMAGE);
        $uploadedFile = new UploadedFile($fixture, self::VALID_IMAGE, 'image/jpeg', null, true);
        $productId = Id::generate();
        $existingMediaId = Id::generate();

        $this->setupContainerWithRequestAndMocks(
            [
                'productId' => (string) $productId,
                'role' => ProductMediaRole::ICON,
                'productMediaId' => (string) $existingMediaId,
            ],
            [$uploadedFile]
        );

        $this->createArticleWithId((string) $productId);
        $this->addProductMediaWithId($existingMediaId, $productId, ProductMediaRole::ICON, ProductMediaRole::THUMBNAIL);

        $controller = oxNew(ArticlePicturesAjax::class);
        $controller->replaceMedia();

        $allMedia = $this->get(ProductMediaDaoInterface::class)->getAll($productId);
        $this->assertCount(2, $allMedia);

        $existingMedia = $this->get(ProductMediaDaoInterface::class)->get($existingMediaId);
        $this->assertFalse($existingMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::ICON)));
        $this->assertTrue($existingMedia->getRoleSet()->has(ProductMediaRole::from(ProductMediaRole::THUMBNAIL)));
    }

    private function createArticleWithId(string $id): void
    {
        $article = oxNew(Article::class);
        $article->setId($id);
        $article->oxarticles__oxshopid = new Field(1);
        $article->oxarticles__oxactive = new Field(1);
        $article->oxarticles__oxtitle = new Field('Test Article');
        $article->oxarticles__oxprice = new Field(10.0);
        $article->save();
    }

    private function addProductMediaWithId(Id $mediaId, Id $productId, string ...$roles): ProductMedia
    {
        $roleSet = new ProductMediaRoleSet(...array_map(
            fn(string $role) => ProductMediaRole::from($role),
            $roles
        ));

        $productMedia = new ProductMedia(
            $mediaId,
            $productId,
            new Media(Id::generate(), new MediaPath('out/pictures/media/test.jpg'), new MediaType('image/jpeg')),
            $roleSet,
        );
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);

        return $productMedia;
    }

    private function setupContainerWithRequest(array $postParameters, array $files = []): void
    {
        $this->createContainer();

        $request = new Request([], $postParameters, [], [], [], [], null);
        if (!empty($files)) {
            $request->files = new FileBag(['uploadedFiles' => $files]);
        }

        $this->container->set(Request::class, $request);
        $this->compileContainer();
        $this->replaceContainerInstance();
    }

    private function setupContainerWithRequestAndMocks(array $postParameters, array $files = []): void
    {
        $this->rewriteProjectConfiguration([
            'parameters' => [
                'oxid_esales.product.media.file.min_size_kb' => '0',
            ]
        ]);

        $this->createContainer();

        $request = new Request([], $postParameters, [], [], [], [], null);
        if (!empty($files)) {
            $request->files = new FileBag(['uploadedFiles' => $files]);
        }

        $mediaUploader = $this->createMock(MediaUploaderInterface::class);
        $mediaUploader
            ->method('uploadTo')
            ->willReturnCallback(function (UploadedFile $file, MediaPath $path) {
                return new MediaPath(
                    Path::join('out/pictures/media/products/placeholder', $file->getClientOriginalName())
                );
            });

        $this->container->set(Request::class, $request);
        $this->container->set('oxid_esales.product.media.media_uploader', $mediaUploader);
        $this->compileContainer();
        $this->replaceContainerInstance();
    }
}
