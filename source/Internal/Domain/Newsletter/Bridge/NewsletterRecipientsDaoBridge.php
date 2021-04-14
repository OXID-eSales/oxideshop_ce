<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao\NewsletterRecipientsDaoInterface;

class NewsletterRecipientsDaoBridge implements NewsletterRecipientsDaoInterface
{
    /**
     * @var NewsletterRecipientsDaoInterface
     */
    private $newsletterRecipientsDao;

    /**
     * NewsletterRecipientsDaoBridge constructor.
     *
     * @param NewsletterRecipientsDaoInterface $newsletterRecipientsDao
     */
    public function __construct(NewsletterRecipientsDaoInterface $newsletterRecipientsDao)
    {
        $this->newsletterRecipientsDao = $newsletterRecipientsDao;
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    public function getNewsletterRecipients(int $shopId): array
    {
        return $this->newsletterRecipientsDao->getNewsletterRecipients($shopId);
    }
}
