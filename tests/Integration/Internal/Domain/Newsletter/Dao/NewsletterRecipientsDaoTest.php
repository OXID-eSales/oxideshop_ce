<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao\NewsletterRecipientsDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class NewsletterRecipientsDaoTest extends TestCase
{
    use ContainerTrait;

    public function testGetNewsletterRecipients(): void
    {
        $recipientsList = $this->get(NewsletterRecipientsDataMapperInterface::class)->mapRecipientListDataToArray(
            $this->get(NewsletterRecipientsDaoInterface::class)->getNewsletterRecipients(1)
        );

        $this->assertEquals(
            [
                [
                    NewsletterRecipientsDataMapper::SALUTATION,
                    NewsletterRecipientsDataMapper::FIRST_NAME,
                    NewsletterRecipientsDataMapper::LAST_NAME,
                    NewsletterRecipientsDataMapper::EMAIL,
                    NewsletterRecipientsDataMapper::OPT_IN_STATE,
                    NewsletterRecipientsDataMapper::COUNTRY,
                    NewsletterRecipientsDataMapper::ASSIGNED_USER_GROUPS,
                ],
                [
                    "MR",
                    "John",
                    "Doe",
                    "admin",
                    "subscribed",
                    "Deutschland",
                    "Shop-Admin,Auslandskunde"
                ]
            ],
            $recipientsList
        );
    }
}
