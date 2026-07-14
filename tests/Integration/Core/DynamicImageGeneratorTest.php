<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\Curl;
use OxidEsales\EshopCommunity\Core\DynamicImageGenerator;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class DynamicImageGeneratorTest extends IntegrationTestCase
{
    private const PARENT_THEME_ID = 'imageParentTheme';
    private const CHILD_THEME_ID = 'imageChildTheme';

    public function setUp(): void
    {
        parent::setUp();

        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $setting = new ShopConfigurationSetting();
        $setting->setName('sDefaultImageQuality');
        $setting->setValue('75');
        $setting->setType(ShopSettingType::STRING);
        $setting->setShopId($shopId);
        $this->get(ShopConfigurationSettingDaoInterface::class)->save($setting);
    }

    public function testRequestToNonExistentImageReturns404(): void
    {
        $url = '/out/pictures/generated/product/1/385_385_75/nonexistent.jpg';

        $curl = new Curl();
        $curl->setMethod('GET');
        $curl->setUrl(rtrim($this->get(ContextInterface::class)->getShopBaseUrl(), '/') . $url);

        $curl->execute();
        $status = $curl->getStatusCode();

        $this->assertEquals(404, $status);
    }

    public function testValidImageGeneration(): void
    {
        $this->createTestMasterImage();

        $url = '/out/pictures/generated/media/products/400_400_75/test-image.jpg';

        $curl = new Curl();
        $curl->setMethod('GET');
        $curl->setUrl(rtrim($this->get(ContextInterface::class)->getShopBaseUrl(), '/') . $url);

        $response = $curl->execute();
        $status = $curl->getStatusCode();

        $this->assertEquals(200, $status);
        $this->assertNotEmpty($response);

        $this->cleanupTestFiles();
    }

    public function testValidImageGenerationForSizeConfiguredOnlyOnParentTheme(): void
    {
        $this->activateChildThemeWithParentDeclaringSize('detailImageSize', '999*999');
        $this->createTestMasterImage();
        $requestUri = $_SERVER['REQUEST_URI'] ?? null;
        $_SERVER['REQUEST_URI'] = '/out/pictures/generated/media/products/999_999_75/test-image.jpg';

        try {
            $imagePath = (new DynamicImageGenerator())->getImagePath();
        } finally {
            if ($requestUri === null) {
                unset($_SERVER['REQUEST_URI']);
            } else {
                $_SERVER['REQUEST_URI'] = $requestUri;
            }
        }

        $this->assertNotFalse($imagePath);
        $this->assertFileExists($imagePath);

        unlink($imagePath);
        rmdir(dirname($imagePath));
        $this->cleanupTestFiles();
    }

    private function activateChildThemeWithParentDeclaringSize(string $settingName, string $value): void
    {
        $context = $this->get(BasicContextInterface::class);
        $shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $parentPath = __DIR__ . '/Fixtures/' . self::PARENT_THEME_ID;
        $dao->save(
            (new ThemeConfiguration())
                ->setId(self::PARENT_THEME_ID)
                ->setSource(Path::makeRelative($parentPath, $context->getShopRootPath()))
                ->addThemeSetting((new Setting())->setName($settingName)->setType('str')->setValue($value)),
            $shopId
        );

        $childPath = __DIR__ . '/Fixtures/' . self::CHILD_THEME_ID;
        $dao->save(
            (new ThemeConfiguration())
                ->setId(self::CHILD_THEME_ID)
                ->setSource(Path::makeRelative($childPath, $context->getShopRootPath()))
                ->setActivated(true),
            $shopId
        );
    }

    private function createTestMasterImage(): void
    {
        $imagePath = $this->getTestImagePath();
        $imageDirectory = dirname($imagePath);
        if (!is_dir($imageDirectory)) {
            mkdir($imageDirectory, 0755, true);
        }

        $image = imagecreate(200, 200);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        imagestring($image, 5, 50, 90, 'TEST', $textColor);
        imagejpeg($image, $imagePath);
    }

    private function cleanupTestFiles(): void
    {
        $imagePath = $this->getTestImagePath();
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    private function getTestImagePath(): string
    {
        return Path::join(
            $this->get(BasicContextInterface::class)->getSourcePath(),
            'out',
            'pictures',
            'media',
            'products',
            'test-image.jpg'
        );
    }
}
