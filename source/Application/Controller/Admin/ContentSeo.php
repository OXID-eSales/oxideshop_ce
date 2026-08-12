<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Content;

/**
 * Content seo config class
 */
class ContentSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Returns url type
     *
     * @return string
     */
    protected function getType()
    {
        return Content::class;
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderContent
     */
    protected function getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class);
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oContent = oxNew(\OxidEsales\Eshop\Application\Model\Content::class);
        if ($oContent->load($this->getEditObjectId())) {
            return $this->getEncoder()->getContentUri($oContent, $this->getEditLang());
        }
    }
}
