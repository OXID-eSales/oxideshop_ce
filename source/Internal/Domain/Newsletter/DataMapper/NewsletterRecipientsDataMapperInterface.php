<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;

interface NewsletterRecipientsDataMapperInterface
{
    /**
     * @param NewsletterRecipient[] $newsletterRecipient
     *
     * @return array
     */
    public function mapRecipientListDataToArray(array $newsletterRecipient): array;
}
