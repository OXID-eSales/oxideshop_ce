<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleImageAlt;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Dao\MediaAttributeDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRoleSet;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

final class ArticleImageAltTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testRenderIncludesAllProductImages(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $thumbnail = $this->addProductMediaWithProductMediaId(
            Id::generate(),
            $productId,
            ProductMediaRole::THUMBNAIL,
            ProductMediaRole::DETAIL
        );
        $icon = $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::ICON);
        $detail1 = $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::DETAIL);
        $detail2 = $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::DETAIL);

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->render();

        $allImages = $controller->getViewData()['allImages'];
        $this->assertCount(4, $allImages);

        $productMediaIds = array_column($allImages, 'productMediaId');
        $this->assertContains((string) $thumbnail->getId(), $productMediaIds);
        $this->assertContains((string) $icon->getId(), $productMediaIds);
        $this->assertContains((string) $detail1->getId(), $productMediaIds);
        $this->assertContains((string) $detail2->getId(), $productMediaIds);
    }

    public function testRenderIncludesRolesAndPosition(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $this->addProductMediaWithProductMediaId(
            Id::generate(),
            $productId,
            ProductMediaRole::THUMBNAIL,
            ProductMediaRole::DETAIL
        );
        $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::ICON);
        $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::DETAIL);

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->render();

        $allImages = $controller->getViewData()['allImages'];

        $thumbnailImage = array_values(array_filter(
            $allImages,
            fn($img) => in_array(ProductMediaRole::THUMBNAIL, $img['roles'])
        ));
        $this->assertCount(1, $thumbnailImage);
        $this->assertContains(ProductMediaRole::DETAIL, $thumbnailImage[0]['roles']);

        $iconImage = array_values(array_filter(
            $allImages,
            fn($img) => in_array(ProductMediaRole::ICON, $img['roles'])
        ));
        $this->assertCount(1, $iconImage);

        $allPositions = array_column($allImages, 'position');
        $this->assertNotEmpty($allPositions);
    }

    public function testRenderReturnsEmptyListForProductWithNoMedia(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->render();

        $allImages = $controller->getViewData()['allImages'];
        $this->assertCount(0, $allImages);
    }

    public function testSaveStoresAllLocalesForSelectedImage(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $productMediaId = Id::generate();
        $this->addProductMediaWithProductMediaId($productMediaId, $productId, ProductMediaRole::DETAIL);

        $request = $this->get(SymfonyRequest::class);
        $request->request->set('productMediaId', (string) $productMediaId);
        $request->request->set(
            'altTexts',
            [(string) $productMediaId => ['de_DE' => 'Deutsches Alt', 'en_GB' => 'English Alt']]
        );

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->save();

        $controller2 = $this->get(ArticleImageAlt::class);
        $controller2->setEditObjectId((string) $productId);
        $controller2->render();

        $savedImage = $controller2->getViewData()['allImages'][0];

        $this->assertSame('Deutsches Alt', $savedImage['altTexts']['de_DE']);
        $this->assertSame('English Alt', $savedImage['altTexts']['en_GB']);
    }

    public function testSaveIgnoresImagesNotSelected(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $selectedProductMedia = $this->addProductMediaWithProductMediaId(
            Id::generate(),
            $productId,
            ProductMediaRole::DETAIL
        );
        $otherProductMedia = $this->addProductMediaWithProductMediaId(
            Id::generate(),
            $productId,
            ProductMediaRole::DETAIL
        );
        $selectedProductMediaId = $selectedProductMedia->getId();
        $otherProductMediaId = $otherProductMedia->getId();
        $selectedMediaId = $selectedProductMedia->getMedia()->getId();
        $otherMediaId = $otherProductMedia->getMedia()->getId();

        $request = $this->get(SymfonyRequest::class);
        $request->request->set('productMediaId', (string) $selectedProductMediaId);
        $request->request->set('altTexts', [
            (string) $selectedProductMediaId => ['de_DE' => 'selected'],
            (string) $otherProductMediaId => ['de_DE' => 'other'],
        ]);

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->save();

        $dao = $this->get(MediaAttributeDaoInterface::class);
        $this->assertSame('selected', $dao->getAttributes($selectedMediaId, 'de_DE', 1)->getAlt());
        $this->assertFalse($dao->getAttributes($otherMediaId, 'de_DE', 1)->has('alt'));
    }

    public function testSaveRemovesAltWhenTextIsEmpty(): void
    {
        $productId = Id::generate();
        $this->createArticleWithId((string) $productId);

        $productMedia = $this->addProductMediaWithProductMediaId(Id::generate(), $productId, ProductMediaRole::DETAIL);
        $productMediaId = $productMedia->getId();
        $mediaId = $productMedia->getMedia()->getId();

        $request = $this->get(SymfonyRequest::class);
        $request->request->set('productMediaId', (string) $productMediaId);
        $request->request->set('altTexts', [(string) $productMediaId => ['de_DE' => 'Deutsches Alt']]);

        $controller = $this->get(ArticleImageAlt::class);
        $controller->setEditObjectId((string) $productId);
        $controller->save();

        $request->request->set('altTexts', [(string) $productMediaId => ['de_DE' => '']]);

        $controller2 = $this->get(ArticleImageAlt::class);
        $controller2->setEditObjectId((string) $productId);
        $controller2->save();

        $controller3 = $this->get(ArticleImageAlt::class);
        $controller3->setEditObjectId((string) $productId);
        $controller3->render();

        $savedImage = $controller3->getViewData()['allImages'][0];
        $this->assertSame('', $savedImage['altTexts']['de_DE']);

        $this->assertFalse(
            $this->get(MediaAttributeDaoInterface::class)
                ->getAttributes($mediaId, 'de_DE', 1)
                ->has('alt')
        );
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

    private function addProductMediaWithProductMediaId(
        Id $productMediaId,
        Id $productId,
        string ...$roles
    ): ProductMedia {
        $roleSet = new ProductMediaRoleSet(...array_map(
            fn(string $role) => ProductMediaRole::from($role),
            $roles
        ));

        $productMedia = new ProductMedia(
            $productMediaId,
            $productId,
            new Media(Id::generate(), new MediaPath('out/pictures/media/test.jpg'), new MediaType('image/jpeg')),
            $roleSet,
        );
        $this->get(ProductMediaServiceInterface::class)->add($productMedia);

        return $productMedia;
    }
}
