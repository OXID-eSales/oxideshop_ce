<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 16.10.18
 * Time: 12:39
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao\ProjectYamlDao;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

class ProjectYamlDaoTest extends TestCase
{

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private $dao;

    public function setUp()
    {
        $context = new ContextStub();
        $context->setShopDir(__DIR__);

        $this->dao = new ProjectYamlDao($context);

    }

    public function testLoading() {

        $testData = <<<EOT
imports:
  -
    resource: /some/non/existing/path/services.yaml

EOT;
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDaoInterface::PROJECT_FILE_NAME,
            $testData);

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertArrayHasKey('imports', $projectYaml->getConfigAsArray());


    }

    public function testLoadingEmptyFile() {

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDaoInterface::PROJECT_FILE_NAME,
            '');

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());

    }

    public function testLoadingNonExistingFile() {

        try {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . ProjectYamlDaoInterface::PROJECT_FILE_NAME);
        } catch (\Exception $e){
            // pass
        }
        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(0, $projectYaml->getConfigAsArray());


    }

    public function testWriting() {

        $projectYaml = new DIConfigWrapper(['imports' => [['resource' => 'some/path']],
                                            'services' => ['somekey' => ['factory' => ['some/factory', 'someMethod']]]]);
        $this->dao->saveProjectConfigFile($projectYaml);

        $projectYaml = $this->dao->loadProjectConfigFile();
        $this->assertCount(2, $projectYaml->getConfigAsArray());
        $this->assertEquals('some/path', $projectYaml->getConfigAsArray()['imports'][0]['resource']);

    }

}
