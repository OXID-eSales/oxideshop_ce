<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Application\Model\Content;

/**
 * Class ContentFactory
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class ContentFactory
{

    /**
     * @param string $key
     * @param string $value
     *
     * @return null|Content
     * @throws \Exception
     */
    public function getContent($key, $value)
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
