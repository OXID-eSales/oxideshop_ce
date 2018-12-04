<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16.10.18
 * Time: 12:39
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Utility\Context;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

class ProjectYamlDaoTest extends TestCase
{
    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private $dao;

    public function setUp()
    {
        /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
        $containerBuilder = new ContainerBuilder(new Facts());
        $container = $containerBuilder->getContainer();

        $contextDefinition = $container->getDefinition(ContextInterface::class);
        $contextDefinition->setClass(ContextStub::class);

        $context = $container->get(ContextInterface::class);
        $context->setShopDir(__DIR__);

        $daoDefinition = $container->getDefinition(ProjectYamlDaoInterface::class);
        $daoDefinition->setPublic(true);

        $container->compile();

        $this->dao = $container->get(ProjectYamlDaoInterface::class);
    }

    public function testLoading()
    {
        $testData = <<<EOT
imports:
  -
    resource: /some/non/existing/path/services.yaml

EOT;
        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDao::PROJECT_FILE_NAME,
            $testData
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertArrayHasKey('imports', $projectYaml->getConfigAsArray());
    }

    public function testLoadingEmptyFile()
    {
        file_put_contents(
            __DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDao::PROJECT_FILE_NAME,
            ''
        );

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testLoadingNonExistingFile()
    {
        try {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDao::PROJECT_FILE_NAME);
        } catch (\Exception $e) {
            // pass
        }
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
        $context = new Context(Registry::getConfig());
        $projectYaml = new DIConfigWrapper([]);

        // Make sure that the cache file exists
        ContainerFactory::getInstance()->getContainer();
        $this->assertFileExists($context->getContainerCacheFile());

        // This should trigger the event that deletes the cachefile
        $this->dao->saveProjectConfigFile($projectYaml);

        $this->assertFileNotExists($context->getContainerCacheFile());

        ContainerFactory::getInstance()->getContainer();
        // Verify container has been rebuild be checking that a cachefile exists
        $this->assertFileExists($context->getContainerCacheFile());
    }
}
