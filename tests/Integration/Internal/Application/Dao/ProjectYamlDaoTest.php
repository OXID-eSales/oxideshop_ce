<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ProjectYamlDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private $dao;

    public function setUp()
    {
        $contextStub = $this->getMockBuilder(BasicContext::class)
            ->setMethods(['getSourcePath'])->getMock();
        $contextStub->method('getSourcePath')->willReturn(__DIR__);
        $this->dao = new ProjectYamlDao($contextStub);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $projectFilePath = __DIR__ . DIRECTORY_SEPARATOR . BasicContext::GENERATED_PROJECT_FILE_NAME;
        if (file_exists($projectFilePath)) {
            unlink($projectFilePath);
        }
    }

    public function testLoading()
    {
        $testData = <<<EOT
imports:
  -
    resource: /some/non/existing/path/services.yaml

EOT;
        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . BasicContext::GENERATED_PROJECT_FILE_NAME,
            $testData
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertArrayHasKey('imports', $projectYaml->getConfigAsArray());
    }

    public function testLoadingEmptyFile()
    {
        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . BasicContext::GENERATED_PROJECT_FILE_NAME,
            ''
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testLoadingNonExistingFile()
    {
        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testWriting()
    {

        $projectYaml = new DIConfigWrapper(
            ['imports'  => [['resource' => 'some/path']],
                'services' => ['somekey' => ['factory' => ['some/factory', 'someMethod']]]]
        );
        $this->dao->saveProjectConfigFile($projectYaml);

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(2, $projectYaml->getConfigAsArray());
        $this->assertEquals('some/path', $projectYaml->getConfigAsArray()['imports'][0]['resource']);
    }

    public function testClearingCacheOnWriting()
    {
        $dao = $this->get(ProjectYamlDaoInterface::class);
        $context = new BasicContext();

        $projectYaml = new DIConfigWrapper([]);

        // Make sure that the cache file exists
        ContainerFactory::resetContainer();
        ContainerFactory::getInstance()->getContainer();
        $this->assertFileExists($context->getContainerCacheFilePath());

        // This should trigger the event that deletes the cachefile
        $dao->saveProjectConfigFile($projectYaml);

        $this->assertFileNotExists($context->getContainerCacheFilePath());

        ContainerFactory::getInstance()->getContainer();
        // Verify container has been rebuild be checking that a cachefile exists
        $this->assertFileExists($context->getContainerCacheFilePath());
    }
}
