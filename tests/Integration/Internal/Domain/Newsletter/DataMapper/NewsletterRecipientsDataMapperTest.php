<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Bridge\NewsletterRecipientsDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapperInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class NewsletterRecipientsDataMapperTest
 */
class NewsletterRecipientsDataMapperTest extends TestCase
{
    use ContainerTrait;

    public function testMapRecipientListDataToArray(): void
    {
        $recipientsList = $this->get(NewsletterRecipientsDaoBridgeInterface::class);
        $data = $recipientsList->getNewsletterRecipients(1);

        $mappedArray = [
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
        ];

        $this->assertEquals(
            $mappedArray,
            $this->get(NewsletterRecipientsDataMapperInterface::class)->mapRecipientListDataToArray($data)
        );

    }
}
