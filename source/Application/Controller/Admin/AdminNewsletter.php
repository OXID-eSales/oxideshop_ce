<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Bridge\NewsletterRecipientsDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge\FileGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileResponseFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\HttpFoundation\Response;

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

    public function export(): Response
    {
        $recipients = $this->getNewsLetterRecipientsList();

        return ContainerFacade::get(FileResponseFactoryInterface::class)->fromCallback(
            function () use ($recipients): void {
                $this->generateCSV($recipients);
            },
            'text/csv; charset=utf-8',
            'Export_user_recipient_status_' . date('Y-m-d') . '.csv'
        );
    }

    private function getNewsLetterRecipientsList(): array
    {
        return ContainerFacade::get(NewsletterRecipientsDaoBridgeInterface::class)
            ->getNewsletterRecipients(
                ContainerFacade::get(ContextInterface::class)
                    ->getCurrentShopId()
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
