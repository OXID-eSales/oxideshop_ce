<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Locale\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;

class LocaleDataMapper implements LocaleDataMapperInterface
{
    public function toData(Locale $locale): array
    {
        return [
            'code' => $locale->getCode(),
            'name' => $locale->getName(),
            'fallback'   => $locale->getFallbackCode(),
        ];
    }

    public function fromData(array $data): Locale
    {
        return new Locale(
            code: $data['code'],
            name: $data['name'],
            fallbackCode: $data['fallback'],
        );
    }
}
