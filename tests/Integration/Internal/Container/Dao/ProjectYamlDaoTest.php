<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Dao;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Container\Fixtures\CE\DummyExecutor;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProjectYamlDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private ProjectYamlDao $dao;

    public function setup(): void
    {
        parent::setUp();

        $contextStub = $this->getMockBuilder(BasicContext::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getGeneratedServicesFilePath'])->getMock();
        $contextStub
            ->method('getGeneratedServicesFilePath')
            ->willReturn($this->getTestGeneratedServicesFilePath());

        $this->dao = new ProjectYamlDao(
            $contextStub,
            $this->get('oxid_esales.symfony.file_system')
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $projectFilePath = $this->getTestGeneratedServicesFilePath();
        if (file_exists($projectFilePath)) {
            unlink($projectFilePath);
        }
    }

    public function testLoading(): void
    {
        $testData = <<<EOT
imports:
  -
    resource: /some/non/existing/path/services.yaml

EOT;
        file_put_contents(
            $this->getTestGeneratedServicesFilePath(),
            $testData
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertArrayHasKey('imports', $projectYaml->getConfigAsArray());
    }

    public function testConvertsAbsolutePathsToRelativeOnSaving(): void
    {
        $configArray = ['imports' => [['resource' => '/some/non/existing/path/services.yaml']]];
        $wrapper = new DIConfigWrapper($configArray);

        $this->dao->saveProjectConfigFile($wrapper);

        $imports = $this->dao->loadProjectConfigFile()->getImportFileNames();
        $this->assertTrue(Path::isRelative($imports[0]));
    }

    public function testLoadingEmptyFile(): void
    {
        file_put_contents(
            $this->getTestGeneratedServicesFilePath(),
            ''
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testLoadingNonExistingFile(): void
    {
        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testWriting(): void
    {
        $projectYaml = new DIConfigWrapper(
            [
                'imports'  => [['resource' => 'some/path']],
                'services' => [
                    'somekey' => [
                        'class' => DummyExecutor::class,
                        'factory' => ['some/factory', 'someMethod']
                    ]
                ]
            ]
        );
        $this->dao->saveProjectConfigFile($projectYaml);

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(2, $projectYaml->getConfigAsArray());
        $this->assertEquals('some/path', $projectYaml->getConfigAsArray()['imports'][0]['resource']);
    }

    public function testClearingCacheOnWriting(): void
    {
        $container = (new ContainerBuilder(new BasicContext()))->getContainer();
        $container->getDefinition(ProjectYamlDaoInterface::class)->setPublic(true);
        $container->compile();

        $dao = $container->get(ProjectYamlDaoInterface::class);
        $context = $container->get(BasicContextInterface::class);

        $projectYaml = $dao->loadProjectConfigFile();

        // Make sure that the cache file exists
        ContainerFactory::resetContainer();
        ContainerFactory::getInstance()->getContainer();
        $this->assertFileExists($context->getContainerCacheFilePath($context->getCurrentShopId()));

        // This should trigger the event that deletes the cachefile
        $dao->saveProjectConfigFile($projectYaml);

        $this->assertFileDoesNotExist($context->getContainerCacheFilePath($context->getCurrentShopId()));

        ContainerFactory::resetContainer();
        ContainerFactory::getInstance()->getContainer();
        // Verify container has been rebuild be checking that a cachefile exists
        $this->assertFileExists($context->getContainerCacheFilePath($context->getCurrentShopId()));
    }

    private function getTestGeneratedServicesFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'generated_project.yaml';
    }
}
