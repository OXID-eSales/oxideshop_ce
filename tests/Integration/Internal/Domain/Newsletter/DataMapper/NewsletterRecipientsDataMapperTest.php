<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Newsletter\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper\NewsletterRecipientsDataMapper;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;
use PHPUnit\Framework\TestCase;

final class NewsletterRecipientsDataMapperTest extends TestCase
{
    public function testMapRecipientListDataToArray(): void
    {
        $recipient = new NewsletterRecipient();
        $recipient
            ->setEmail('someMail')
            ->setFistName('Soca')
            ->setLastName('Warrior')
            ->setSalutation('no')
            ->setCountry('Trinidad und Tobago')
            ->setOtpInState('1')
            ->setUserGroups('someString');

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
                "no",
                "Soca",
                "Warrior",
                "someMail",
                "subscribed",
                "Trinidad und Tobago",
                "someString"
            ]
        ];

        $this->assertEquals(
            $mappedArray,
            (new NewsletterRecipientsDataMapper())->mapRecipientListDataToArray([$recipient])
        );
    }
}
