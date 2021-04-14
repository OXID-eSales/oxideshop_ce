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
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;
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
                NewsletterRecipientsDataMapper::SALUTATION           => "MR",
                NewsletterRecipientsDataMapper::FIRST_NAME           => "John",
                NewsletterRecipientsDataMapper::LAST_NAME            => "Doe",
                NewsletterRecipientsDataMapper::EMAIL                => "admin",
                NewsletterRecipientsDataMapper::OPT_IN_STATE         => "subscribed",
                NewsletterRecipientsDataMapper::COUNTRY              => "Deutschland",
                NewsletterRecipientsDataMapper::ASSIGNED_USER_GROUPS => "Auslandskunde,Shop-Admin"
            ]
        ];

        $this->assertEquals(
            $mappedArray,
            $this->get(NewsletterRecipientsDataMapperInterface::class)->mapRecipientListDataToArray($data)
        );

    }
}
