<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Application\Model\Content;

class ContentFactory
{
    /**
     * @throws \Exception
     */
    public function getContent(string $key, string $value): ?Content
    {
        $content = oxNew(Content::class);

        if ('ident' === $key) {
            $isLoaded = $content->loadbyIdent($value);
        } elseif ('oxid' === $key) {
            $isLoaded = $content->load($value);
        } else {
            throw new \Exception('Cannot load content. Not provided neither ident nor oxid.');
        }

        return $isLoaded ? $content : null;
    }
}
