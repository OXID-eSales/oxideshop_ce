<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
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
        return ContainerFacade::get(NewsletterRecipientsDaoBridgeInterface::class)
            ->getNewsletterRecipients(
                ContainerFacade::get(ContextInterface::class)
                    ->getCurrentShopId()
            );
    }

    private function setCSVHeader(): void
    {
        ContainerFacade::get(HeaderGeneratorBridgeInterface::class)
            ->generate(
                'Export_recipients_' . date('Y-m-d') . '.csv'
            );
    }

    /**
     * @param array $data
     */
    private function generateCSV(array $data): void
    {
        ContainerFacade::get(FileGeneratorBridgeInterface::class)
            ->generate(
                'php://output',
                ContainerFacade::get(NewsletterRecipientsDataMapperInterface::class)
                    ->mapRecipientListDataToArray($data)
            );
    }
}
