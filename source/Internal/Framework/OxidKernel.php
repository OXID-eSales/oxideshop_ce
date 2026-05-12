<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\RoutePass;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\CompilerPass\ViewControllerPass;
use OxidEsales\EshopCommunity\Internal\Framework\Twig\OxidTwigLoaderPass;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Http\LegacyController;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

class OxidKernel extends Kernel
{
    use MicroKernelTrait;

    private BasicContextInterface $basicContext;
    private int $shopId;
    private ?ContainerBuilder $containerBuilder = null;
    /** @var array<int, array{0: CompilerPassInterface, 1: string, 2: int}> */
    private array $extraCompilerPasses = [];

    public function __construct(string $environment, bool $debug)
    {
        $this->basicContext = new BasicContext();
        $this->shopId = (new ShopIdCalculator(new UtilsServer()))->getShopId();
        parent::__construct($environment, $debug);
    }

    public function addCompilerPass(
        CompilerPassInterface $pass,
        string $type = \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION,
        int $priority = 0
    ): self {
        $this->extraCompilerPasses[] = [$pass, $type, $priority];
        return $this;
    }

    /** @return iterable<BundleInterface> */
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();

        $bundlesFile = Path::join($this->getProjectDir(), 'var/configuration/bundles.yaml');
        if (!is_readable($bundlesFile)) {
            return;
        }

        $config = Yaml::parseFile($bundlesFile);
        foreach ($config['bundles'] ?? [] as $class => $envs) {
            if (($envs['all'] ?? false) || ($envs[$this->getEnvironment()] ?? false)) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return $this->basicContext->getShopRootPath();
    }

    public function getCacheDir(): string
    {
        $base = $this->basicContext->getCacheDirectory() ?: sys_get_temp_dir() . '/oxid_cache';
        return Path::join($base, 'shop_' . $this->shopId);
    }

    public function getLogDir(): string
    {
        return Path::join($this->basicContext->getSourcePath(), 'log');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routesFile = Path::join($this->getProjectDir(), 'var/configuration/routes.yaml');
        if (is_readable($routesFile)) {
            $routes->import($routesFile);
        }

        $this->importServiceControllerRoutes($routes);

        $routes->import($this->basicContext->getSourcePath() . '/Internal/Framework/Http/LegacyController.php', 'attribute');
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->parameters()->set('oxid_secret_default', md5($this->getProjectDir()));

        $configDir = Path::join($this->getProjectDir(), 'var/configuration');
        foreach (['framework.yaml', 'packages.yaml'] as $file) {
            $path = Path::join($configDir, $file);
            if (is_readable($path)) {
                $container->import($path);
            }
        }
    }

    protected function build(ContainerBuilder $container): void
    {
        $this->containerBuilder = $container;

        $container->setParameter('oxid_esales.current_shop_id', $this->shopId);
        $container->setParameter('oxid_esales.shop_source_directory', $this->basicContext->getSourcePath());

        $container->addCompilerPass(new ViewControllerPass());
        $container->addCompilerPass(new RoutePass());
        $container->addCompilerPass(new OxidTwigLoaderPass(), \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);

        $this->loadEditionServices($container);
        $this->loadComponentServices($container);
        $this->loadModuleServices($container);
        $this->loadProjectServices($container);
        $this->loadProjectSubshopServices($container);
        $this->loadEnvironmentServices($container);
        $this->loadSubshopEnvironmentServices($container);

        foreach ($this->extraCompilerPasses as [$pass, $type, $priority]) {
            $container->addCompilerPass($pass, $type, $priority);
        }
    }

    private function importServiceControllerRoutes(RoutingConfigurator $routes): void
    {
        if (!$this->containerBuilder) {
            return;
        }

        foreach ($this->containerBuilder->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if (
                !$definition->isPublic()
                || $definition->isAbstract()
                || !$class
                || !class_exists($class)
                || is_a($class, LegacyController::class, true)
            ) {
                continue;
            }

            $ref = new \ReflectionClass($class);
            if ($ref->getFileName() && $this->hasRouteAttributes($ref)) {
                $routes->import($ref->getFileName(), 'attribute');
            }
        }
    }

    private function hasRouteAttributes(\ReflectionClass $class): bool
    {
        if ($class->getAttributes(RouteAttribute::class)) {
            return true;
        }
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (
                $method->getDeclaringClass()->getName() === $class->getName()
                && $method->getAttributes(RouteAttribute::class)
            ) {
                return true;
            }
        }
        return false;
    }

    private function loadEditionServices(ContainerBuilder $container): void
    {
        foreach ($this->getEditionsRootPaths() as $editionPath) {
            $this->yamlLoader($container, [$editionPath])->load('Internal/services.yaml');
        }
    }

    private function getEditionsRootPaths(): array
    {
        return match ($this->basicContext->getEdition()) {
            Edition::Community => [
                $this->basicContext->getEditionSourcePath(Edition::Community),
            ],
            Edition::Professional => [
                $this->basicContext->getEditionSourcePath(Edition::Community),
                $this->basicContext->getEditionSourcePath(Edition::Professional),
            ],
            Edition::Enterprise => [
                $this->basicContext->getEditionSourcePath(Edition::Community),
                $this->basicContext->getEditionSourcePath(Edition::Professional),
                $this->basicContext->getEditionSourcePath(Edition::Enterprise),
            ],
        };
    }

    private function loadComponentServices(ContainerBuilder $container): void
    {
        $this->loadYamlIfExists($this->yamlLoader($container), $this->basicContext->getGeneratedServicesFilePath(), $container);
    }

    private function loadModuleServices(ContainerBuilder $container): void
    {
        $path = $this->basicContext->getActiveModuleServicesFilePath($this->shopId);
        try {
            $this->loadYamlIfExists($this->yamlLoader($container), $path, $container);
        } catch (LoaderLoadException $exception) {
            (new LoggerServiceFactory(new Context($this->shopId)))
                ->getLogger()
                ->error("Can't load module services: $path", [$exception]);
        }
    }

    private function loadProjectServices(ContainerBuilder $container): void
    {
        $this->loadProjectExtensionFiles($container, $this->basicContext->getProjectConfigurationDirectory());
    }

    private function loadProjectSubshopServices(ContainerBuilder $container): void
    {
        $this->loadProjectExtensionFiles($container, $this->basicContext->getShopConfigurationDirectory($this->shopId));
    }

    private function loadEnvironmentServices(ContainerBuilder $container): void
    {
        $this->loadProjectExtensionFiles(
            $container,
            EnvUrlFormatter::toEnvUrl($this->basicContext->getProjectConfigurationDirectory())
        );
    }

    private function loadSubshopEnvironmentServices(ContainerBuilder $container): void
    {
        $this->loadProjectExtensionFiles(
            $container,
            Path::join(
                EnvUrlFormatter::toEnvUrl($this->basicContext->getProjectConfigurationDirectory()),
                Path::makeRelative(
                    $this->basicContext->getShopConfigurationDirectory($this->shopId),
                    $this->basicContext->getProjectConfigurationDirectory()
                )
            )
        );
    }

    private function loadProjectExtensionFiles(ContainerBuilder $container, string $configurationUrl): void
    {
        foreach (['services.yaml', 'parameters.yaml'] as $file) {
            $this->loadYamlIfExists($this->yamlLoader($container), Path::join($configurationUrl, $file), $container);
        }
    }

    private function yamlLoader(ContainerBuilder $container, array $paths = []): YamlFileLoader
    {
        return new YamlFileLoader($container, new FileLocator($paths));
    }

    private function loadYamlIfExists(YamlFileLoader $loader, string $yamlFile, ContainerBuilder $container): void
    {
        try {
            $loader->load($yamlFile);
        } catch (FileLocatorFileNotFoundException) {
            $container->addResource(new FileExistenceResource($yamlFile));
        }
    }
}
