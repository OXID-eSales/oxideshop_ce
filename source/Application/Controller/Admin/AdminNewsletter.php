<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Bridge\NewsletterRecipientsDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge\FileGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Header\Bridge\HeaderGeneratorBridgeInterface;

/**
 * Admin newsletter manager.
 * Returns template, that arranges template ("newsletter") to frame.
 * Admin Menu: Customer Info -> Newsletter.
 */
class AdminNewsletter extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'newsletter';

    public function export(): void
    {
        $newsletterRecipientsList = $this->getNewsLetterRecipientsList();
        $this->setCSVHeader();
        $this->generateCSV($newsletterRecipientsList);

        $oUtils = Registry::getUtils();
        $oUtils->showMessageAndExit("");
    }

    private function getNewsLetterRecipientsList(): array
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopId = $container->get(ContextInterface::class)->getCurrentShopId();
        $recipientsList = $container->get(NewsletterRecipientsDaoBridgeInterface::class);
        return $recipientsList->getNewsletterRecipients($shopId);
    }

    private function setCSVHeader(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $csvHeader = $container->get(HeaderGeneratorBridgeInterface::class);

        $filename = "Export_recipients_" . date("Y-m-d") . ".csv";
        $csvHeader->generate($filename);
    }

    /**
     * @param array $data
     */
    private function generateCSV(array $data): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $csvGenerator = $container->get(FileGeneratorBridgeInterface::class);
        $csvGenerator->generate(
            "php://output",
            $container->get(NewsletterRecipientsDataMapperInterface::class)->mapRecipientListDataToArray($data)
        );
    }
}
