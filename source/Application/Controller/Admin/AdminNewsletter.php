<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Bridge\NewsletterRecipientsDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge\FileGeneratorBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Header\Bridge\HeaderGeneratorBridgeInterface;
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

        $newsletterRecipientsList = $this->getNewsLetterRecipientsList($container);
        $this->generateCSV($container, $newsletterRecipientsList);

        $this->setCSVHeader($container);

        $oUtils = Registry::getUtils();
        $oUtils->showMessageAndExit("");
    }

    private function getNewsLetterRecipientsList(ContainerInterface $container): array
    {
        $shopId = $container->get(ContextInterface::class)->getCurrentShopId();
        $recipientsList = $container->get(NewsletterRecipientsDaoBridgeInterface::class);
        return $recipientsList->get($shopId);
    }

    private function setCSVHeader(ContainerInterface $container): void
    {
        $csvHeader = $container->get(HeaderGeneratorBridgeInterface::class);

        $filename = "Export_recipients_" . date("Y-m-d") . ".csv";
        $csvHeader->generate($filename);
    }

    /**
     * @param ContainerInterface $container
     * @param array              $data
     */
    private function generateCSV(ContainerInterface $container, array $data): void
    {
        $csvGenerator = $container->get(FileGeneratorBridgeInterface::class);
        $csvGenerator->generate("php://output", $this->mapRecipientListDataToArray($data));
    }

    /**
     * @param NewsletterRecipient[] $newsletterRecipient
     *
     * @return array
     */
    private function mapRecipientListDataToArray(array $newsletterRecipient): array
    {
        $result = [];

        foreach ($newsletterRecipient as $index => $value) {
            $result[$index][$value::SALUTATION] = $this->sanitizeSemicolon($value->getSalutation());
            $result[$index][$value::FIRST_NAME] = $this->sanitizeSemicolon($value->getFistName());
            $result[$index][$value::LAST_NAME] = $this->sanitizeSemicolon($value->getLastName());
            $result[$index][$value::EMAIL] = $this->sanitizeSemicolon($value->getEmail());
            $result[$index][$value::OPT_IN_STATE] = $value->getOtpInState();
            $result[$index][$value::COUNTRY] = $this->sanitizeSemicolon($value->getCountry());
            $result[$index][$value::ASSIGNED_USER_GROUPS] = $value->getUserGroups();
        }

        return $result;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function sanitizeSemicolon(string $str): string
    {
        return trim($str, ";");
    }
}
