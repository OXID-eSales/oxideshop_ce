<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\NewsletterRecipients;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge\FileGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\HeaderGenerator\Bridge\HeaderGeneratorBridgeInterface;
use Psr\Container\ContainerInterface;

/**
 * Admin newsletter manager.
 * Returns template, that arranges template ("newsletter.tpl") to frame.
 * Admin Menu: Customer Info -> Newsletter.
 */
class AdminNewsletter extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'newsletter.tpl';

    public function export(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $this->setCSVHeader($container);

        $newsletterRecipients = new NewsletterRecipients();
        $newsletterRecipientsList = $newsletterRecipients->getNewsletterRecipients();
        $this->generateCSV($container, $newsletterRecipientsList);

        exit();
    }

    private function setCSVHeader(ContainerInterface $container): void
    {
        $csvHeader = $container->get(HeaderGeneratorBridgeInterface::class);

        $filename = "Export_recipients_" . date("Y-m-d") . ".csv";
        $csvHeader->generate($filename);
    }

    private function generateCSV(ContainerInterface $container, array $data): void
    {
        $csvGenerator = $container->get(FileGeneratorBridgeInterface::class);
        $csvGenerator->generate("php://output", $data);
    }
}
