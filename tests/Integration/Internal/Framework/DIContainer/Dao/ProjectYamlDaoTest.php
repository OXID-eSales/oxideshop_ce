<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ProjectYamlDaoTest extends TestCase
{
    use ContainerTrait;

    private string $tmpFixture = __DIR__ . DIRECTORY_SEPARATOR . 'generated_project.yaml';
    private ProjectYamlDao $dao;

    public function setup(): void
    {
        parent::setUp();

        (new Filesystem())->touch($this->tmpFixture);
        $context = new BasicContextStub();
        $context->setGeneratedServicesFilePath($this->tmpFixture);

        $this->dao = new ProjectYamlDao(
            $context,
            $this->get('oxid_esales.symfony.file_system')
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();

        (new Filesystem())->remove($this->tmpFixture);
    }

    public function testLadProjectConfigFileWillWorkWithEmptyFile(): void
    {
        (new Filesystem())->remove($this->tmpFixture);

        $this->assertEmpty($this->dao->loadProjectConfigFile()->getConfigAsArray());

        (new Filesystem())->dumpFile($this->tmpFixture, '');

        $this->assertEmpty($this->dao->loadProjectConfigFile()->getConfigAsArray());
    }

    public function testSaveProjectConfigFileWillUseRelativePaths(): void
    {
        $relativePath = 'some/relative/path/services.yaml';
        $absolutePath = '/some/absolute/path/services.yaml';
        $configArray = [
            'imports' => [
                ['resource' => $relativePath],
                ['resource' => $absolutePath],
            ],
        ];

        $this->dao->saveProjectConfigFile(new DIConfigWrapper($configArray));

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertEquals($relativePath, $projectYaml->getImportFileNames()[0]);
        $this->assertTrue(Path::isRelative($projectYaml->getImportFileNames()[1]));
    }

    public function testSaveProjectConfigFileWillClearContainerCache(): void
    {
        $cacheFile = $this->get(ContextInterface::class)->getContainerCacheFilePath(
            $this->get(BasicContextInterface::class)->getDefaultShopId()
        );
        $this->assertFileExists($cacheFile);

        $this->get(ProjectYamlDaoInterface::class)->saveProjectConfigFile(
            $this->get(ProjectYamlDaoInterface::class)->loadProjectConfigFile()
        );

        $this->assertFileDoesNotExist($cacheFile);
    }
}
