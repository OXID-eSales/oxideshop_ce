<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use Symfony\Component\DependencyInjection\Definition;
use Webmozart\PathUtil\Path;

class ServicesYamlValidator implements ModuleConfigurationValidatorInterface
{
    /** @var BasicContextInterface  */
    private $basicContext;

    /** @var ProjectYamlDaoInterface  */
    private $projectYamlDao;

    public function __construct(
        BasicContextInterface $basicContext,
        ProjectYamlDaoInterface $projectYamlDao
    ) {
        $this->basicContext = $basicContext;
        $this->projectYamlDao = $projectYamlDao;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int $shopId
     * @throws \Throwable
     */
    public function validate(ModuleConfiguration $configuration, int $shopId)
    {
        $projectYaml = $this->projectYamlDao->loadProjectConfigFile();
        $originalProjectYaml = clone $projectYaml;

        try {
            $projectYaml->addImport(
                Path::join($this->basicContext->getModulesPath(), $configuration->getPath(), 'services.yaml')
            );
            $this->projectYamlDao->saveProjectConfigFile($projectYaml);

            $container = $this->buildContainer();
            $this->checkContainer($container);
        } catch (NoServiceYamlException $e) {
            return;
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            // Restore the old settings
            $this->projectYamlDao->saveProjectConfigFile($originalProjectYaml);
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     * @throws \Exception
     */
    private function buildContainer(): \Symfony\Component\DependencyInjection\ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder($this->basicContext);
        $container = $containerBuilder->getContainer();
        foreach ($container->getDefinitions() as $definitionKey => $definition) {
            /** @var Definition $definition $isPublic */
            $definition->setPublic(true);
        }
        $container->compile();
        return $container;
    }

    /**
     * Try to fetch all services defined
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Exception
     */
    private function checkContainer(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $definitionKey => $definition) {
            $container->get($definitionKey);
        }
    }
}
