<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\Curl;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Filesystem\Path;

final class DynamicImageGeneratorTest extends IntegrationTestCase
{
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

        $url = '/out/pictures/generated/media/products/100_100_75/test-image.jpg';

        $curl = new Curl();
        $curl->setMethod('GET');
        $curl->setUrl(rtrim($this->get(ContextInterface::class)->getShopBaseUrl(), '/') . $url);

        $response = $curl->execute();
        $status = $curl->getStatusCode();

        $this->assertEquals(200, $status);
        $this->assertNotEmpty($response);

        $this->cleanupTestFiles();
    }

    #[DataProvider('maliciousRequestProvider')]
    public function testDirectRequestCannotDiscloseFiles(string $url): void
    {
        $curl = new Curl();
        $curl->setMethod('GET');
        $curl->setUrl(rtrim($this->get(ContextInterface::class)->getShopBaseUrl(), '/') . $url);

        $response = $curl->execute();

        $this->assertSame(404, $curl->getStatusCode());
        $this->assertSame('', $response);
    }

    public static function maliciousRequestProvider(): array
    {
        return [
            'query string path traversal' => [
                '/getimg.php?out/pictures/generated/../../../../composer.json',
            ],
            'URL encoded query string path traversal' => [
                '/getimg.php?out/pictures/generated/%2e%2e/%2e%2e/%2e%2e/index.php',
            ],
        ];
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
