<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Directory;

use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class DirectoryServiceTest extends TestCase
{
    use ContainerTrait;

    /** @var DirectoryService */
    private $directoryService;

    protected function setUp(): void
    {
        $this->directoryService = $this->getDirectoryService();

        parent::setUp();
    }

    public function testDirectoriesExistent(): void
    {
        $this->directoryService->checkDirectoriesExistent();
    }

    public function testDirectoriesPermission(): void
    {
        $this->directoryService->checkDirectoriesPermission();
    }

    /**
     * @return DirectoryService
     */
    private function getDirectoryService(): DirectoryService
    {
        $context = $this->get(BasicContextInterface::class);

        return new DirectoryService($context);
    }
}
