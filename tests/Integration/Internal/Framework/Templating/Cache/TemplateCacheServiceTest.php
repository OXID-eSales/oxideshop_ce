<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class TemplateCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private $templateCacheDirectory;

    /** @var Filesystem */
    private $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->templateCacheDirectory = __DIR__ . '/Fixtures/tmp/smarty';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->filesystem->dumpFile(
            Path::join($this->templateCacheDirectory, 'sample.tpl.php'),
            ""
        );

        parent::tearDown();
    }

    public function testInvalidateTemplateCache(): void
    {
        $this->putFilesInTemplateCache();

        $basicContext = $this->getMockBuilder(BasicContext::class)
            ->setMethods(['getCacheDirectory'])
            ->getMock();
        $basicContext->method('getCacheDirectory')->willReturn(__DIR__ . '/Fixtures/tmp');

        $templateCacheService = new TemplateCacheService($basicContext, $this->filesystem);

        $templateCacheService->invalidateTemplateCache();

        self::assertCount(
            0,
            glob("{$this->templateCacheDirectory}/*.php")
        );
    }

    private function putFilesInTemplateCache(): void
    {
        $templates = [
            'notice.tpl.php',
            'errors.tpl.php',
            'success.tpl.php'
        ];

        foreach ($templates as $template) {
            $this->filesystem->dumpFile(
                Path::join($this->templateCacheDirectory, $template),
                ""
            );
        }

        self::assertCount(
            4,
            glob("{$this->templateCacheDirectory}/*.tpl.php")
        );
    }
}
