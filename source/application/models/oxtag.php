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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

if (!defined('OXTAGCLOUD_MINTAGLENGTH')) {
    define('OXTAGCLOUD_MINTAGLENGTH', 4);
}
/**
 * Class dedicated to tags handling
 *
 * @package model
 */
class oxTag extends oxSuperCfg
{

    /**
     * Tag value
     *
     * @var string
     */
    protected $_sTag = '';

    /**
     * Forbidden tags
     */
    protected $_aForbiddenTags = array(
        'admin', 'application', 'core', 'export', 'modules', 'out', 'setup', 'tmp'
    );

    /**
     * Formatted tag link
     *
     * @var string
     */
    protected $_sTagLink = null;

    /**
     * Maximum tag's length
     * Maximum size of one tag in admin area and limits tag input field in front end
     *
     * @var int
     */
    protected $_iTagMaxLength = 60;

    /**
     * Tag frequency
     *
     * @var int
     */
    protected $_iHitCount = 1;

    /**
     * Meta characters.
     * Array of meta chars used for FULLTEXT index.
     *
     * @var array
     */
    protected $_aMetaChars = array('+','-','>','<','(',')','~','*','"','\'','\\','[',']','{','}',';',':','.','/','|','!','@','#','$','%','^','&','?','=','`');

    /**
     * Sets tag value
     *
     * @param string $sTag tag value
     */
    public function __construct( $sTag = null )
    {
        parent::__construct();
        if ( $sTag !== null ) {
            $this->set( $sTag );
        }
    }

    /**
     * Sets maximum tag length
     *
     * @param int $iTagMaxLength Tag maximum length
     *
     * @return int
     */
    public function setMaxLength( $iTagMaxLength )
    {
        $this->_iTagMaxLength = $iTagMaxLength;
    }

    /**
     * Returns maximum tag length
     *
     * @return int
     */
    public function getMaxLength()
    {
        return $this->_iTagMaxLength;
    }

    /**
     * Sets tag value
     *
     * @param string $sTag      Tag value
     * @param bool   $blPrepare if false, no checks will be done when setting
     *
     * @return null
     */
    public function set( $sTag, $blPrepare = true )
    {
        $this->_sTag = $blPrepare? $this->prepare( $sTag ) : $sTag;
        $this->setLink();
    }

    /**
     * Sets tag value
     *
     * @return string Tag value
     */
    public function get()
    {
        return $this->_sTag;
    }

    /**
     * Sets tag size value
     *
     * @param int $iHitCount size of tag
     *
     * @return void
     */
    public function setHitCount( $iHitCount )
    {
        $this->_iHitCount = $iHitCount;
    }

    /**
     * Returns tag size value
     *
     * @return int Tag size value
     */
    public function getHitCount()
    {
        return $this->_iHitCount;
    }

    /**
     * Increases tag size value
     *
     * @return void
     */
    public function increaseHitCount()
    {
        $this->_iHitCount++;
    }

    /**
     * Checks if tag is valid
     *
     * @return bool
     */
    public function isValid()
    {
        $blValid = strlen($this->_sTag) > 0? true : false;
        if ( $blValid && in_array( $this->_sTag, $this->_aForbiddenTags ) ) {
            $blValid = false;
        }
        return $blValid;
    }

    /**
     * Returns tag url (seo or dynamic depends on shop mode)
     *
     * @return string
     */
    public function getLink()
    {
        if ( is_null($this->_sTagLink) ) {
             $this->_sTagLink = $this->formLink( $this->get() );
        }
        return $this->_sTagLink;
    }

    /**
     * Sets tag url. If nothing is passed, link is reset to null
     *
     * @param string $sTagLink formed tag link
     *
     * @return string
     */
    public function setLink( $sTagLink = null )
    {
        $this->_sTagLink = $sTagLink;
    }

    /**
     * Returns html safe tag title
     *
     * @return string
     */
    public function getTitle()
    {
        return getStr()->htmlentities( $this->get() );
    }

    /**
     * Renders tag
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     * Takes tag string, checks its length and makes longer tag shorter if needed.
     * Also trims it and removes unnecessary characters.
     *
     * @param string $sTag tag value
     *
     * @return object oxTag
     */
    public function prepare( $sTag )
    {
        $sTag = $this->stripMetaChars($sTag);
        $oStr = getStr();
        $iLen = $oStr->strlen( $sTag );
        if ( $iLen > $this->getMaxLength() ) {
            $sTag = trim( $oStr->substr( $sTag, 0, $this->getMaxLength() ) );
        }
        return $oStr->strtolower( $sTag );
    }

    /**
     * Changes any mysql specific meta characters with spaces
     *
     * @param string $sText given text
     *
     * @return string
     */
    public function stripMetaChars( $sText )
    {
        $oStr  = getStr();

        // Remove meta chars
        $sText = str_replace($this->_aMetaChars, ' ', $sText);

        // Replace multiple spaces with single space
        $sText = $oStr->preg_replace( "/\s+/", " ", trim( $sText ) );

        return $sText;
    }

    /**
     * Returns tag url (seo or dynamic depends on shop mode)
     *
     * @param string $sTag tag
     *
     * @return string
     */
    public function formLink( $sTag )
    {
        $oSeoEncoderTag = oxRegistry::get("oxSeoEncoderTag");

        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $sUrl = false;
        if ( oxRegistry::getUtils()->seoIsActive() ) {
            $sUrl = $oSeoEncoderTag->getTagUrl( $sTag, $iLang );
        }
        return $sUrl ? $sUrl : $this->getConfig()->getShopUrl() . $oSeoEncoderTag->getStdTagUri( $sTag ) . "&amp;lang=" . $iLang;
    }

    /**
     * Adds dashes to too short tag words, so that they would be equal to minTagLength
     *
     * @return void
     */
    public function addUnderscores()
    {
        $oStr = getStr();
        $aTagParts = explode( ' ', $this->get() );
        foreach ($aTagParts as &$sTagPart) {
            if ( $oStr->strlen( $sTagPart ) < OXTAGCLOUD_MINTAGLENGTH ) {
                $sTagPart .= str_repeat( "_", OXTAGCLOUD_MINTAGLENGTH - $oStr->strlen( $sTagPart ) );
            }
        }
        unset($sTagPart);
        $this->set( implode( ' ', $aTagParts ), false );
    }


    /**
     * Removes dashes from tag words.
     *
     * @return string
     */
    public function removeUnderscores()
    {
        $oStr = getStr();
        $sRes = '';
        if ( $oStr->preg_match_all( "/([\s\-]?)([^\s\-]+)([\s\-]?)/", $this->get(), $aMatches ) ) {
            foreach ( $aMatches[2] as $iKey => $sMatch ) {
                if ( $oStr->strlen( $sMatch ) <= OXTAGCLOUD_MINTAGLENGTH ) {
                    $sMatch = rtrim( $sMatch, "_" );
                }
                $sRes .= $aMatches[1][$iKey] . $sMatch . $aMatches[3][$iKey];
            }
        }
        $this->set( $sRes, false );
    }
}