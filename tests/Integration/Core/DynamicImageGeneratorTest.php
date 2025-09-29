<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\Curl;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

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

        $this->assertEquals(404, $status, );
    }

    public function testValidImageGeneration(): void
    {
        $this->createTestMasterImage();

        $url = '/out/pictures/generated/media/product/100_100_75/test-image.jpg';

        $curl = new Curl();
        $curl->setMethod('GET');
        $curl->setUrl(rtrim($this->get(ContextInterface::class)->getShopBaseUrl(), '/') . $url);

        $response = $curl->execute();
        $status = $curl->getStatusCode();

        $this->assertEquals(200, $status);
        $this->assertNotEmpty($response);

        $this->cleanupTestFiles();
    }

    private function createTestMasterImage(): void
    {
        $masterDir = __DIR__ . '/../../../source/out/pictures/media/product';
        if (!is_dir($masterDir)) {
            mkdir($masterDir, 0755, true);
        }

        $imagePath = $masterDir . '/test-image.jpg';

        $image = imagecreate(200, 200);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        imagestring($image, 5, 50, 90, 'TEST', $textColor);
        imagejpeg($image, $imagePath);
        imagedestroy($image);
    }

    private function cleanupTestFiles(): void
    {
        $filePath = __DIR__ . '/../../../source/out/pictures/media/product/test-image.jpg';
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
