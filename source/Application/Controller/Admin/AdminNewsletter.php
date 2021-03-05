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
        $this->setCSVHeader();
        $this->generateCSV((new NewsletterRecipients())->getNewsletterRecipients());
        exit();
    }

    private function setCSVHeader(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $filename = "Export_recipients_" . date("Y-m-d") . ".csv";
        $csvHeader = $container->get(HeaderGeneratorBridgeInterface::class);
        $csvHeader->generate($filename);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function generateCSV(array $data): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $csvGenerator = $container->get(FileGeneratorBridgeInterface::class);
        $csvGenerator->generate("php://output", $data);
    }
}
