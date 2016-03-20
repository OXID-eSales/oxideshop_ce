<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

/**
 * Seo encoder for tags.
 */
class oxSeoEncoderTag extends oxSeoEncoder
{
    /** @var oxTagCloud Tag preparation util object. */
    protected $_oTagPrepareUtil = null;

    /**
     * Returns SEO uri for tag.
     *
     * @param string $tag        Tag name
     * @param int    $languageId Language id
     * @param string $objectId   Object id [optional]
     *
     * @return string
     */
    public function getTagUri($tag, $languageId = null, $objectId = null)
    {
        return $this->_getDynamicTagUri($tag, $this->getStdTagUri($tag), "tag/{$tag}/", $languageId, $objectId);
    }

    /**
     * Returns dynamic object SEO URI.
     *
     * @param string $tag        Tag name
     * @param string $stdUrl     Standard url
     * @param string $seoUrl     Seo uri
     * @param int    $languageId Active language
     * @param string $articleId  Article id [optional]
     *
     * @return string
     */
    protected function _getDynamicTagUri($tag, $stdUrl, $seoUrl, $languageId, $articleId = null)
    {
        $shopId = $this->config->getShopId();

        $stdUrl = $this->_trimUrl($stdUrl);
        $objectId = $this->getDynamicObjectId($shopId, $stdUrl);
        $seoUrl = $this->_prepareUri($this->addLanguageParam($seoUrl, $languageId), $languageId);

        //load details link from DB
        $oldSeoUrl = $this->_loadFromDb('dynamic', $objectId, $languageId);
        if ($oldSeoUrl === $seoUrl) {
            $seoUrl = $oldSeoUrl;
        } else {
            if ($oldSeoUrl) {
                // old must be transferred to history
                $this->_copyToHistory($objectId, $shopId, $languageId, 'dynamic');
            }
            // creating unique
            $seoUrl = $this->_processSeoUrl($seoUrl, $objectId, $languageId);

            // inserting
            $this->_saveToDb('dynamic', $objectId, $stdUrl, $seoUrl, $languageId, $shopId);
        }

        return $seoUrl;
    }

    /**
     * Prepares tag for search in db
     *
     * @param string $tag tag to prepare
     *
     * @deprecated since v5.3.0 (2015-08-18), use oxTag::prepare() instead.
     *
     * @return string
     */
    protected function _prepareTag($tag)
    {
        if ($this->_oTagPrepareUtil == null) {
            $this->_oTagPrepareUtil = oxNew('oxTag');
        }

        return $tag = $this->_oTagPrepareUtil->prepare($tag);
    }

    /**
     * Returns standard tag url.
     * While tags are just strings, standard ulrs formatted stays here.
     *
     * @param string $tag                Tag name
     * @param bool   $shouldIncludeIndex If you need only parameters, set this to false (optional)
     *
     * @return string
     */
    public function getStdTagUri($tag, $shouldIncludeIndex = true)
    {
        $uri = "cl=tag&amp;searchtag=" . rawurlencode($tag);
        if ($shouldIncludeIndex) {
            $uri = "index.php?" . $uri;
        }

        return $uri;
    }

    /**
     * Returns full url for passed tag
     *
     * @param string $tag        Tag name
     * @param int    $languageId Language id
     *
     * @return string
     */
    public function getTagUrl($tag, $languageId = null)
    {
        if (!isset($languageId)) {
            $languageId = oxRegistry::getLang()->getBaseLanguage();
        }

        return $this->_getFullUrl($this->getTagUri($tag, $languageId), $languageId);
    }

    /**
     * Returns tag SEO url for specified page.
     *
     * @param string $tag        Tag name
     * @param int    $pageNumber Page to prepare number
     * @param int    $languageId Language id
     * @param bool   $isFixed    Fixed url marker (default is false)
     *
     * @return string
     */
    public function getTagPageUrl($tag, $pageNumber, $languageId = null, $isFixed = false)
    {
        if (!isset($languageId)) {
            $languageId = oxRegistry::getLang()->getBaseLanguage();
        }
        $stdUrl = $this->getStdTagUri($tag) . '&amp;pgNr=' . $pageNumber;
        $parameters = (int) ($pageNumber + 1);

        $stdUrl = $this->_trimUrl($stdUrl, $languageId);
        $seoUrl = $this->getTagUri($tag, $languageId) . $parameters . "/";

        return $this->_getFullUrl($this->_getDynamicTagUri($tag, $stdUrl, $seoUrl, $languageId), $languageId);
    }
}
