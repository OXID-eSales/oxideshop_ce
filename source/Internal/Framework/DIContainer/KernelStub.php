<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 */
class KernelStub extends Kernel
{
    private string $oxidProjectDir;
    private string $oxidCacheDir;
    private string $oxidLogDir;

    /** @var BundleInterface[] */
    private array $oxidBundles;

    private ContainerBuilder $oxidContainerBuilder;

    /**
     * @param BundleInterface[] $bundles Keyed by bundle name
     */
    public function __construct(
        string $environment,
        bool $debug,
        string $projectDir,
        string $cacheDir,
        string $logDir,
        array $bundles,
        ContainerBuilder $oxidContainerBuilder,
    ) {
        $this->oxidProjectDir = $projectDir;
        $this->oxidCacheDir = $cacheDir;
        $this->oxidLogDir = $logDir;
        $this->oxidBundles = $bundles;
        $this->oxidContainerBuilder = $oxidContainerBuilder;

        parent::__construct($environment, $debug);

        $this->bundles = $bundles;
    }

    public function registerBundles(): iterable
    {
        return array_values($this->oxidBundles);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        throw new \LogicException('OXID does not use Symfony HttpKernel for request handling.');
    }

    public function locateResource(string $name): string
    {
        if (!isset($name[0]) || '@' !== $name[0]) {
            throw new \InvalidArgumentException(sprintf('A resource name must start with @ ("%s" given).', $name));
        }

        $pos = strpos($name, '/');
        $bundleName = substr($name, 1, $pos !== false ? $pos - 1 : null);
        $resourcePath = $pos !== false ? substr($name, $pos) : '';

        if (!isset($this->oxidBundles[$bundleName])) {
            throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or is not enabled.', $bundleName));
        }

        $file = $this->oxidBundles[$bundleName]->getPath() . $resourcePath;

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('Unable to find file "%s".', $name));
        }

        return $file;
    }

    public function getProjectDir(): string
    {
        return $this->oxidProjectDir;
    }

    public function getCacheDir(): string
    {
        return $this->oxidCacheDir;
    }

    public function getBuildDir(): string
    {
        return $this->oxidCacheDir;
    }

    public function getLogDir(): string
    {
        return $this->oxidLogDir;
    }

    public function setContainer(\Psr\Container\ContainerInterface $container): void
    {
        $this->container = $container;
    }

    protected function initializeBundles(): void
    {
    }

    protected function buildContainer(): SymfonyContainerBuilder
    {
        return $this->oxidContainerBuilder->getContainer();
    }

    protected function getContainerBuilder(): SymfonyContainerBuilder
    {
        $container = new SymfonyContainerBuilder();
        $container->getParameterBag()->add([
            'kernel.project_dir' => $this->oxidProjectDir,
            'kernel.environment' => $this->environment,
            'kernel.debug' => $this->debug,
            'kernel.charset' => 'UTF-8',
            'kernel.container_class' => 'OxidContainer',
            'kernel.cache_dir' => $this->oxidCacheDir,
            'kernel.build_dir' => $this->oxidCacheDir,
            'kernel.logs_dir' => $this->oxidLogDir,
        ]);

        return $container;
    }

    protected function prepareContainer(SymfonyContainerBuilder $container): void
    {
        foreach ($this->oxidBundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
            }
        }

        foreach ($this->oxidBundles as $bundle) {
            $bundle->build($container);
        }

        $extensions = array_keys($container->getExtensions());
        $container->getCompilerPassConfig()->setMergePass(
            new MergeExtensionConfigurationPass($extensions)
        );
    }

    protected function build(SymfonyContainerBuilder $container): void
    {
    }
}
