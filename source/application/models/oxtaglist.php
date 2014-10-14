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

if (!defined('OXTAGCLOUD_MINFONT')) {
    define('OXTAGCLOUD_MINTAGLENGTH', 4);
    define('OXTAGCLOUD_STARTPAGECOUNT', 20);
    define('OXTAGCLOUD_EXTENDEDCOUNT', 200);
}
/**
 * Class dedicated to article tags handling.
 * Is responsible for saving, returning and adding tags for given article.
 *
 * @package model
 */
class oxTagList extends oxI18n implements oxITagList
{

    /**
     * Tags array.
     *
     * @var array
     */
    protected $_oTagSet = null;

    /**
     * Extended mode
     *
     * @var bool
     */
    protected $_blExtended = false;

    /**
     * Instantiates oxtagset object
     */
    public function __construct()
    {
        parent::__construct();
        $this->_oTagSet = oxNew( 'oxtagset' );
    }

    /**
     * Returns cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        return 'tag_list_'.$this->getLanguage();
    }

    /**
     * Loads all articles tags list.
     *
     * @return bool
     */
    public function loadList()
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $iLang = $this->getLanguage();

        $sArtView  = getViewName( 'oxarticles', $iLang );
        $sViewName = getViewName( 'oxartextends', $iLang );

        // check if article is still active
        $oArticle   = oxNew( 'oxarticle' );
        $oArticle->setLanguage( $iLang );
        $sArtActive = $oArticle->getActiveCheckQuery( true );

        $sQ = "SELECT {$sViewName}.`oxtags` AS `oxtags`
            FROM {$sArtView} AS `oxarticles`
                LEFT JOIN {$sViewName} ON `oxarticles`.`oxid` = {$sViewName}.`oxid`
            WHERE `oxarticles`.`oxactive` = 1 AND $sArtActive";

        $oDb->setFetchMode( oxDb::FETCH_MODE_ASSOC );
        $oRs = $oDb->select( $sQ );

        $this->get()->clear();
        while ( $oRs && $oRs->recordCount() && !$oRs->EOF ) {
            $this->_addTagsFromDb( $oRs->fields['oxtags'] );
            $oRs->moveNext();
        }

        return $this->_isLoaded = true;
    }

    /**
     * Returns oxTagSet list
     *
     * @return oxTagSet
     */
    public function get()
    {
        return $this->_oTagSet;
    }

    /**
     * Adds tag to list
     *
     * @param string $mTag tag as string or as oxTag object
     *
     * @return void
     */
    public function addTag( $mTag )
    {
        $this->_oTagSet->addTag( $mTag );
    }

    /**
     * Adds record from database to tagset
     *
     * @param string $sTags tags string to add
     *
     * @return void
     */
    protected function _addTagsFromDb( $sTags )
    {
        if ( empty( $sTags ) ) {
            return;
        }
        $sSeparator = $this->get()->getSeparator();
        $aTags = explode( $sSeparator, $sTags );
        foreach ( $aTags as $sTag ) {
            $oTag = oxNew( "oxtag" );
            $oTag->set( $sTag, false );
            $oTag->removeUnderscores();
            $this->addTag( $oTag );
        }
    }
}