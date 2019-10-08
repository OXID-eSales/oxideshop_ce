<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Application\Model\Content;

class ContentFactory
{

    /**
     * @param string $key
     * @param string $value
     *
     * @return null|Content
     * @throws \Exception
     */
    public function getContent(string $key, string $value): ?Content
    {
        $content = oxNew("oxcontent");

        if ($key == 'ident') {
            $isLoaded = $content->loadbyIdent($value);
        } elseif ($key == 'oxid') {
            $isLoaded = $content->load($value);
        } else {
            throw new \Exception("Cannot load content. Not provided neither ident nor oxid.");
        }

        return $isLoaded ? $content : null;
    }
}
