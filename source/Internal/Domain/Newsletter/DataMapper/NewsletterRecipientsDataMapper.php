<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;

/**
 * Class NewsletterRecipientsDataMapper
 */
class NewsletterRecipientsDataMapper implements NewsletterRecipientsDataMapperInterface
{
    public const SALUTATION = 'Salutation';
    public const FIRST_NAME = 'Firstname';
    public const LAST_NAME = 'LastName';
    public const EMAIL = 'Email';
    public const OPT_IN_STATE = 'Opt-In state';
    public const COUNTRY = 'Country';
    public const ASSIGNED_USER_GROUPS = 'Assigned user groups';

    /**
     * @param NewsletterRecipient[] $newsletterRecipient
     *
     * @return array
     */
    public function mapRecipientListDataToArray(array $newsletterRecipient): array
    {
        $result = [
            [
                self::SALUTATION,
                self::FIRST_NAME,
                self::LAST_NAME,
                self::EMAIL,
                self::OPT_IN_STATE,
                self::COUNTRY,
                self::ASSIGNED_USER_GROUPS,
            ],
        ];

        foreach ($newsletterRecipient as $value) {
            $result[] = [
                $value->getSalutation(),
                $value->getFistName(),
                $value->getLastName(),
                $value->getEmail(),
                $value->getOtpInState(),
                $value->getCountry(),
                $value->getUserGroups(),
            ];
        }

        return $result;
    }
}
