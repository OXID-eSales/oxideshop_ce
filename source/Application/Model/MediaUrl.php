<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Media URL handler
 *
 */
class MediaUrl extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxmediaurls';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxmediaurls');
    }

    /**
     * Return HTML code depending on current URL
     *
     * @return string
     */
    public function getHtml()
    {
        $sUrl = $this->oxmediaurls__oxurl->value;
        //youtube link
        if (strpos($sUrl, 'youtube.com') || strpos($sUrl, 'youtu.be')) {
            return $this->_getYoutubeHtml();
        }

        //simple link
        return $this->getHtmlLink();
    }

    /**
     * Returns simple HTML link
     *
     * @param bool $blNewPage Whether to open link in new window (adds target=_blank to link)
     *
     * @return string
     */
    public function getHtmlLink($blNewPage = true)
    {
        $sForceBlank = $blNewPage ? ' target="_blank"' : '';
        $sDesc = $this->oxmediaurls__oxdesc->value;
        $sUrl = $this->getLink();

        $sHtmlLink = "<a href=\"$sUrl\"{$sForceBlank}>$sDesc</a>";

        return $sHtmlLink;
    }

    /**
     * Returns  link
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->oxmediaurls__oxisuploaded->value) {
            $sUrl = $this->getConfig()->isSsl() ? $this->getConfig()->getSslShopUrl() : $this->getConfig()->getShopUrl();
            $sUrl .= 'out/media/';
            $sUrl .= basename($this->oxmediaurls__oxurl->value);
        } else {
            $sUrl = $this->oxmediaurls__oxurl->value;
        }

        return $sUrl;
    }

    /**
     * Returns  object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->oxmediaurls__oxobjectid->value;
    }

    /**
     * Deletes record and unlinks the file
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        $sFilePath = $this->getConfig()->getConfigParam('sShopDir') . "/out/media/" .
                     basename($this->oxmediaurls__oxurl->value);

        if ($this->oxmediaurls__oxisuploaded->value) {
            if (file_exists($sFilePath)) {
                unlink($sFilePath);
            }
        }

        return parent::delete($sOXID);
    }

    /**
     * Transforms the link to YouTube object, and returns it.
     *
     * @return string
     */
    protected function _getYoutubeHtml()
    {
        $sUrl = $this->oxmediaurls__oxurl->value;
        $sDesc = $this->oxmediaurls__oxdesc->value;

        if (strpos($sUrl, 'youtube.com')) {
            $sYoutubeUrl = str_replace("www.youtube.com/watch?v=", "www.youtube.com/embed/", $sUrl);
            $sYoutubeUrl = preg_replace('/&amp;/', '?', $sYoutubeUrl, 1);
        }
        if (strpos($sUrl, 'youtu.be')) {
            $sYoutubeUrl = str_replace("youtu.be/", "www.youtube.com/embed/", $sUrl);
        }

        $sYoutubeTemplate = '%s<br><iframe width="425" height="344" src="%s" frameborder="0" allowfullscreen></iframe>';
        $sYoutubeHtml = sprintf($sYoutubeTemplate, $sDesc, $sYoutubeUrl, $sYoutubeUrl);

        return $sYoutubeHtml;
    }
}
