<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\TemplateExtension;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;

/**
 * @internal
 */
class TemplateBlockExtensionDao implements TemplateBlockExtensionDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * TemplateBlockDao constructor.
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     */
    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @param TemplateBlockExtension $templateBlockExtension
     */
    public function add(TemplateBlockExtension $templateBlockExtension)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxtplblocks')
            ->values([
                'oxid'          => 'uuid()',
                'oxshopid'      => ':shopId',
                'oxmodule'      => ':moduleId',
                'oxblockname'   => ':name',
                'oxfile'        => ':filePath',
                'oxtemplate'    => ':templatePath',
                'oxpos'         => ':priority',
                'oxactive'      => '1',
            ])
            ->setParameters([
                'shopId'        => $templateBlockExtension->getShopId(),
                'moduleId'      => $templateBlockExtension->getModuleId(),
                'name'          => $templateBlockExtension->getName(),
                'filePath'      => $templateBlockExtension->getFilePath(),
                'templatePath'  => $templateBlockExtension->getExtendedBlockTemplatePath(),
                'priority'      => $templateBlockExtension->getPriority(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $name
     * @param int    $shopId
     * @return array
     */
    public function getExtensions(string $name, int $shopId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('*')
            ->from('oxtplblocks')
            ->where('oxshopid = :shopId')
            ->andWhere('oxblockname = :name')
            ->andWhere('oxmodule != \'\'')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $name,
            ]);

        $blocksData = $queryBuilder->execute()->fetchAll();

        return $this->mapDataToObjects($blocksData);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deleteExtensions(string $moduleId, int $shopId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxtplblocks')
            ->where('oxshopid = :shopId')
            ->andWhere('oxmodule = :moduleId')
            ->setParameters([
                'shopId'    => $shopId,
                'moduleId'  => $moduleId,
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param array $blocksData
     * @return array
     */
    private function mapDataToObjects(array $blocksData): array
    {
        $templateBlockExtensions = [];

        foreach ($blocksData as $blockData) {
            $templateBlock = new TemplateBlockExtension();
            $templateBlock
                ->setShopId(
                    (int) $blockData['OXSHOPID']
                )
                ->setModuleId(
                    $blockData['OXMODULE']
                )
                ->setName(
                    $blockData['OXBLOCKNAME']
                )
                ->setFilePath(
                    $blockData['OXFILE']
                )
                ->setExtendedBlockTemplatePath(
                    $blockData['OXTEMPLATE']
                )
                ->setPriority(
                    (int) $blockData['OXPOS']
                );

            $templateBlockExtensions[] = $templateBlock;
        }

        return $templateBlockExtensions;
    }
}
