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
 * user list "view" class.
 * @package admin
 */
class List_Review extends Article_List
{
    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType  = 'oxlist';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxreview';

    /**
     * Viewable list size getter
     *
     * @return int
     */
    protected function _getViewListSize()
    {
        return $this->_getUserDefListSize();
    }

    /**
     * Executes parent method parent::render(), passes data to Smarty engine
     * and returns name of template file "list_review.tpl".
     *
     * @return string
     */
    public function render()
    {
        oxAdminList::render();

        $this->_aViewData["menustructure"] = $this->getNavigation()->getDomXml()->documentElement->childNodes;
        $this->_aViewData["articleListTable"] = getViewName('oxarticles');

        return "list_review.tpl";
    }

    /**
     * Returns select query string
     *
     * @param object $oObject list item object
     *
     * @return string
     */
    protected function _buildSelectString( $oObject = null )
    {
        $sArtTable = getViewName( 'oxarticles', $this->_iEditLang );

        $sQ  = "select oxreviews.oxid, oxreviews.oxcreate, oxreviews.oxtext, oxreviews.oxobjectid, {$sArtTable}.oxparentid, {$sArtTable}.oxtitle as oxtitle, {$sArtTable}.oxvarselect as oxvarselect, oxparentarticles.oxtitle as parenttitle, ";
        $sQ .= "concat( {$sArtTable}.oxtitle, if(isnull(oxparentarticles.oxtitle), '', oxparentarticles.oxtitle), {$sArtTable}.oxvarselect) as arttitle from oxreviews ";
        $sQ .= "left join $sArtTable as {$sArtTable} on {$sArtTable}.oxid=oxreviews.oxobjectid and 'oxarticle' = oxreviews.oxtype ";
        $sQ .= "left join $sArtTable as oxparentarticles on oxparentarticles.oxid = {$sArtTable}.oxparentid ";
        $sQ .= "where 1 and oxreviews.oxlang = '{$this->_iEditLang}' ";


        //removing parent id checking from sql
        $sStr = "/\s+and\s+".$sArtTable."\.oxparentid\s*=\s*''/";
        $sQ = getStr()->preg_replace( $sStr, " ", $sQ );

        return " $sQ and {$sArtTable}.oxid is not null ";
    }

    /**
     * Adds filtering conditions to query string
     *
     * @param array  $aWhere filter conditions
     * @param string $sSql   query string
     *
     * @return string
     */
    protected function _prepareWhereQuery( $aWhere, $sSql )
    {
        $sSql = parent::_prepareWhereQuery( $aWhere, $sSql );

        $sArtTable = getViewName( 'oxarticles', $this->_iEditLang );
        $sArtTitleField = "{$sArtTable}.oxtitle";

        // if searching in article title field, updating sql for this case
        if ( $this->_aWhere[$sArtTitleField] ) {
            $sSqlForTitle = " (CONCAT( {$sArtTable}.oxtitle, if(isnull(oxparentarticles.oxtitle), '', oxparentarticles.oxtitle), {$sArtTable}.oxvarselect)) ";
            $sSql = getStr()->preg_replace( "/{$sArtTable}\.oxtitle\s+like/", "$sSqlForTitle like", $sSql );
        }

        return $sSql;
    }
}
