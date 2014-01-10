<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @deprecated
 */

/**
 * Shop information manager.
 * Renders and displays default or passed by URL template.
 */
class Info extends oxUBase
{
    /**
     * Delivery list
     * @var object
     */
    protected $_oDelList = null;

    /**
     * Delivery set list
     * @var object
     */
    protected $_oDelSetList = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Content object
     * @var object
     */
    protected $_oContent = null;

    /**
     * Class constructor, assigns template file name passed by URL
     * or stored in session ("tpl", "infotpl").
     *
     * Template variables:
     * <b>tpl</b>
     *
     * Session variables:
     * <b>infotpl</b>
     */
    public function info()
    {
        // assign template name
        $sTplName = oxConfig::getParameter( 'tpl');
        $sTplName = $sTplName ? $sTplName : oxSession::getVar( 'infotpl' );

        if ( $sTplName ) {
            // security fix so that you cant access files from outside template dir
            $sTplName = basename( $sTplName );
            oxSession::setVar( 'infotpl', $sTplName );

            $sTplName = 'custom/'.$sTplName;
        }

        $this->_sThisTemplate = $sTplName;
    }


    /**
     * Loads delivery, deliveryset list info and returns name of template file
     * to render info::_sThisTemplate. If no template name specified - will
     * load "impressum" content
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        parent::render();

        if ( !$this->getTemplateName() ) {
            //  get default page
            $oContent = $this->getContent();
            $this->getViewConfig()->setViewConfigParam( 'tpl', $oContent->getId() );
            $this->_sThisTemplate = 'page/info/content.tpl';
        } else {
            $this->getViewConfig()->setViewConfigParam( 'tpl', $this->getTemplateName() );
        }

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns this template name
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns active content
     *
     * @return object
     */
    public function getContent()
    {
        if ( $this->_oContent === null ) {
            //  get default page
            $oContent = oxNew( 'oxcontent' );
            $oContent->loadByIdent( 'oximpressum' );
            $this->_oContent = $oContent;
        }
        return $this->_oContent;
    }

    /**
     * Template variable getter. Returns delivery list
     *
     * @return object
     */
    public function getDeliveryList()
    {
        if ( $this->_oDelList === null ) {
            $this->_oDelList = oxNew( 'oxdeliverylist' );
            $this->_oDelList->getList();
        }
        return $this->_oDelList;
    }

    /**
     * Template variable getter. Returns delivery set list
     *
     * @return object
     */
    public function getDeliverySetList()
    {
        if ( $this->_oDelSetList === null ) {
            $this->_oDelSetList = oxNew( 'oxdeliverysetlist' );
            $this->_oDelSetList->getList();
        }
        return $this->_oDelSetList;
    }
    
    /**
     * Returns content parsed through smarty 
     * 
     * @return string
     */
    public function getParsedContent()
    {        
        return oxUtilsView::getInstance()->parseThroughSmarty( $this->getContent()->oxcontents__oxcontent->value );        
    }

    /**
     * Template title getter.
     *
     * @return string
     */
    public function getTitle()
    {
        $oContent = $this->getContent();
        
        return $oContent->oxcontents__oxtitle->value;
    }
}
