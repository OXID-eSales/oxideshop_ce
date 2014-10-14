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

/**
     * Class dedicated to article tags handling.
     * Is responsible for saving, returning and adding tags for given article.
     *
     * @package model
     */
class oxArticleTagList extends oxI18n implements oxITagList
{

    /**
     * Tags
     *
     * @var string
     */
    protected $_oTagSet = null;

    /**
     * Instantiates oxTagSet object
     */
    public function __construct()
    {
        parent::__construct();
        $this->_oTagSet = oxNew( 'oxTagSet' );
    }

    /**
     * Sets article id
     *
     * @param string $sArticleId Article id
     *
     * @return null
     */
    public function setArticleId( $sArticleId )
    {
        $this->setId( $sArticleId );
    }

    /**
     * Returns current article id
     *
     * @return string
     */
    public function getArticleId()
    {
        return $this->getId();
    }

    /**
     * Returns cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        return null;
    }

    /**
     * Loads article tags from DB. Returns true on success.
     *
     * @param string $sArticleId article id
     *
     * @return bool
     */
    public function load( $sArticleId )
    {
        $this->setArticleId( $sArticleId );
        $oDb = oxDb::getDb();
        $sViewName = getViewName( "oxartextends", $this->getLanguage() );
        $sQ = "select oxtags from {$sViewName} where oxid = ".$oDb->quote( $sArticleId );

        $this->set( "" );
        // adding tags to list. Tags does not need to be checked again, but dashes needs to be removed
        $aTags = explode( $this->get()->getSeparator(), $oDb->getOne( $sQ ) );
        foreach ( $aTags as $sTag ) {
            $oTag = oxNew( "oxtag" );
            $oTag->set( $sTag, false );
            $oTag->removeUnderscores();
            $this->addTag( $oTag );
        }
        return $this->_isLoaded = true;
    }

    /**
     * Loads article tags list.
     *
     * @param string $sArticleId article id
     *
     * @return bool
     */
    public function loadList( $sArticleId = null )
    {
        if ( $sArticleId === null && ( $sArticleId = $this->getArticleId() ) === null) {
            return false;
        }
        return $this->load( $sArticleId );
    }

    /**
     * Saves article tags to DB. Returns true on success.
     *
     * @return bool
     */
    public function save()
    {
        if ( !$this->getArticleId() ) {
            return false;
        }
        $oTagSet = $this->get();
        foreach ( $oTagSet as $oTag ) {
            $oTag->addUnderscores();
        }
        $sTags = oxDb::getInstance()->escapeString( $oTagSet );
        $oDb = oxDb::getDb();

        $sTable = getLangTableName( 'oxartextends', $this->getLanguage() );
        $sLangSuffix = oxRegistry::getLang()->getLanguageTag($this->getLanguage());

        $sQ = "insert into {$sTable} (oxid, oxtags$sLangSuffix) value (".$oDb->quote( $this->getArticleId() ).", '{$sTags}')
               on duplicate key update oxtags$sLangSuffix = '{$sTags}'";

        if ( $oDb->execute( $sQ ) ) {
            $this->executeDependencyEvent();
            return true;
        }
        return false;
    }


    /**
     * Saves article tags
     *
     * @param string $sTags article tag
     *
     * @return bool
     */
    public function set( $sTags )
    {
        return $this->_oTagSet->set( $sTags );
    }

    /**
     * Returns article tags set object
     *
     * @return object;
     */
    public function get()
    {
        return $this->_oTagSet;
    }

    /**
     * Returns article tags array
     *
     * @return object;
     */
    public function getArray()
    {
        return $this->_oTagSet->get();
    }

    /**
     * Adds tag to list
     *
     * @param string $mTag tag as string or as oxTag object
     *
     * @return bool
     */
    public function addTag( $mTag )
    {
        return $this->_oTagSet->addTag( $mTag );
    }

    /**
     * Returns standard product Tag URL
     *
     * @param string $sTag tag
     *
     * @return string
     */
    public function getStdTagLink( $sTag )
    {
        $sStdTagLink = $this->getConfig()->getShopHomeURL( $this->getLanguage(), false );
        return $sStdTagLink . "cl=details&amp;anid=".$this->getId()."&amp;listtype=tag&amp;searchtag=".rawurlencode( $sTag );
    }

    /**
     * Checks if tags was already tagged for the same product
     *
     * @param string $sTagTitle given tag
     *
     * @return bool
     */
    public function canBeTagged( $sTagTitle )
    {
        $aProducts = oxRegistry::getSession()->getVariable("aTaggedProducts");
        if ( isset($aProducts) && $aTags = $aProducts[$this->getArticleId()] ) {
            if ( $aTags[$sTagTitle] == 1 ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Execute cache dependencies
     *
     * @return null
     */
    public function executeDependencyEvent()
    {
        $this->_updateTagDependency();

    }

    /**
     * Execute cache dependencies
     *
     * @return null
     */
    protected function _updateTagDependency()
    {
        // reset tags cloud cache
        $oTagList = oxNew( "oxTagList" );
        $oTagList->setLanguage( $this->getLanguage() );
        $oTagCloud = oxNew( "oxTagCloud" );
        $oTagCloud->setTagList($oTagList);
        $oTagCloud->resetCache();
    }
}