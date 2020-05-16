<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

/**
 * Special page for Credits
 */
class CreditsController extends \OxidEsales\Eshop\Application\Controller\ContentController
{
    /**
     * Content id.
     *
     * @var string
     */
    protected $_sContentId = "oxcredits";

    /**
     * Returns active content id to load its seo meta info
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getSeoObjectId" in next major
     */
    protected function _getSeoObjectId() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getContentId();
    }

    /**
     * Template variable getter. Returns active content
     *
     * @return object
     */
    public function getContent()
    {
        if ($this->_oContent === null) {
            $this->_oContent = false;
            $oContent = oxNew(\OxidEsales\Eshop\Application\Model\Content::class);
            if ($oContent->loadByIdent($this->getContentId())) {
                $this->_oContent = $oContent;
            }
        }

        return $this->_oContent;
    }
}
