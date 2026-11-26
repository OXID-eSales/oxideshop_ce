<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class CachePoolGeneration implements CachePoolGenerationInterface
{
    /**
     * @var array<int, string>
     */
    private array $generations = [];

    public function __construct(
        private readonly BasicContextInterface $context,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getDirectory(int $shopId): string
    {
        $this->generations[$shopId] ??= $this->getCurrentGeneration($shopId);

        return Path::join($this->getShopDirectory($shopId), $this->generations[$shopId]);
    }

    public function renew(int $shopId): void
    {
        $this->generations[$shopId] = bin2hex(random_bytes(4));
        $this->filesystem->dumpFile($this->getGenerationFile($shopId), $this->generations[$shopId]);

        $this->removeOldGenerations($shopId, $this->generations[$shopId]);
    }

    private function getShopDirectory(int $shopId): string
    {
        return Path::join($this->context->getCacheDirectory(), 'pool', 'shops', (string) $shopId);
    }

    private function getGenerationFile(int $shopId): string
    {
        return Path::join($this->getShopDirectory($shopId), 'generation');
    }

    private function getCurrentGeneration(int $shopId): string
    {
        $generationFile = $this->getGenerationFile($shopId);

        return $this->filesystem->exists($generationFile) ? $this->filesystem->readFile($generationFile) : '0';
    }

    private function removeOldGenerations(int $shopId, string $currentGeneration): void
    {
        $oldGenerations = (new Finder())
            ->directories()
            ->in($this->getShopDirectory($shopId))
            ->depth(0)
            ->notName($currentGeneration);

        foreach ($oldGenerations as $oldGeneration) {
            $this->removeDirectory($oldGeneration->getPathname());
        }
    }

    private function removeDirectory(string $directory): void
    {
        try {
            $this->filesystem->remove($directory);
        } catch (IOExceptionInterface $exception) {
            $this->logger->warning('Outdated cache pool cannot be removed', ['exception' => $exception]);
        }
    }
}
