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
 * Admin article files parameters manager.
 * Collects and updates (on user submit) files.
 * Admin Menu: Manage Products -> Articles -> Files.
 * @package admin
 */
class Article_Files extends oxAdminDetails
{
    /**
     * Template name
     *
     * @var unknown_type
     */
    protected $_sThisTemplate = 'article_files.tpl';

    /**
     * Stores editing article
     *
     * @var oxArticle
     */
    protected $_oArticle = null;

    /**
     * Collects available article axtended parameters, passes them to
     * Smarty engine and returns tamplate file name "article_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ( !$this->getConfig()->getConfigParam( 'blEnableDownloads' ) ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_DISABLED_DOWNLOADABLE_PRODUCTS' );
        }
        $oArticle = $this->getArticle();
        // variant handling
        if ( $oArticle->oxarticles__oxparentid->value) {
            $oParentArticle = oxNew( 'oxarticle' );
            $oParentArticle->load( $oArticle->oxarticles__oxparentid->value);
            $oArticle->oxarticles__oxisdownloadable = new oxField( $oParentArticle->oxarticles__oxisdownloadable->value );
            $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves editing article changes (oxisdownloadable)
     * and updates oxFile object which are associated with editing object
     *
     * @return null
     */
    public function save()
    {
        // save article changes
        $aArticleChanges = oxConfig::getParameter('editval');
        $oArticle = $this->getArticle();
        $oArticle->assign($aArticleChanges);
        $oArticle->save();

        //update article files
        $aArticleFiles = oxConfig::getParameter('article_files');
        if (count($aArticleFiles) > 0) {
            foreach ($aArticleFiles as $sArticleFileId => $aArticleFileUpdate) {
                $oArticleFile = oxNew('oxFile');
                $oArticleFile->load($sArticleFileId);
                $aArticleFileUpdate  = $this->_processOptions($aArticleFileUpdate);
                $oArticleFile->assign($aArticleFileUpdate);
                $oArticleFile->save();
            }
        }
    }

    /**
     * Returns current oxarticle object
     *
     * @param bool $blReset Load article again
     *
     * @return oxFile
     */
    public function getArticle($blReset = false)
    {
        if ($this->_oArticle !== null && !$blReset) {
            return $this->_oArticle;
        }
        $sProductId = $this->getEditObjectId();

        $oProduct = oxNew( 'oxArticle' );
        $oProduct->load( $sProductId );
        $this->_oArticle = $oProduct;

        return $this->_oArticle;
    }

    /**
     * Creates new oxFile object and stores newly uploaded file
     *
     * @return null
     */
    public function upload()
    {
        $myConfig = $this->getConfig();

        if ( $myConfig->isDemoShop() ) {
            $oEx = oxNew( "oxExceptionToDisplay" );
            $oEx->setMessage( 'ARTICLE_EXTEND_UPLOADISDISABLED' );
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false );

            return;
        }
        
        $soxId = $this->getEditObjectId();

        $aParams  = oxConfig::getParameter( "newfile");
        $aParams  = $this->_processOptions($aParams);
        $aNewFile = $this->getConfig()->getUploadedFile( "newArticleFile");

        $sExistingFilename = trim(oxConfig::getParameter( "existingFilename"));

        //uploading and processing supplied file
        $oArticleFile = oxNew( "oxFile" );
        $oArticleFile->assign($aParams);

        if (!$aNewFile['name'] && !$oArticleFile->oxfiles__oxfilename->value) {
            return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_NOFILE' );
        }

        if ($aNewFile['name']) {
            $oArticleFile->oxfiles__oxfilename        = new oxField($aNewFile['name'], oxField::T_RAW);
            try {
                $oArticleFile->processFile( 'newArticleFile' );
            } catch (Exception $e) {
                return oxRegistry::get("oxUtilsView")->addErrorToDisplay( $e->getMessage() );
            }
        }

        //save media url
        $oArticleFile->oxfiles__oxartid       = new oxField($soxId, oxField::T_RAW);
        $oArticleFile->save();
    }

    /**
     * Deletes article file from fileid parameter and checks if this file belongs to current article.
     *
     * @return void
     */
    public function deletefile()
    {
        $myConfig = $this->getConfig();

        if ( $myConfig->isDemoShop() ) {
            $oEx = oxNew( "oxExceptionToDisplay" );
            $oEx->setMessage( 'ARTICLE_EXTEND_UPLOADISDISABLED' );
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false );

            return;
        }
        
        $sArticleId = $this->getEditObjectId();
        $sArticleFileId = oxConfig::getParameter('fileid');
        $oArticleFile = oxNew('oxFile');
        $oArticleFile->load($sArticleFileId);
        if ($oArticleFile->hasValidDownloads()) {
            return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_DELETING_VALID_FILE' );
        }
        if ($oArticleFile->oxfiles__oxartid->value == $sArticleId) {
            $oArticleFile->delete();
        }
    }

    /**
     * Returns real config option value
     *
     * @param int $iOption option value
     *
     * @return int
     */
    public function getConfigOptionValue( $iOption )
    {
        $iOption = ( $iOption < 0 ) ? "" : $iOption;
        return $iOption;
    }

    /**
     * Process config options. If value is not set, save as "-1" to database
     *
     * @param array $aParams params
     *
     * @return array
     */
    protected function _processOptions($aParams)
    {
        if (!is_array($aParams)) {
            $aParams = array();
        }

        if (!isset($aParams["oxfiles__oxdownloadexptime"]) || $aParams["oxfiles__oxdownloadexptime"] == "") {
            $aParams["oxfiles__oxdownloadexptime"] = -1;
        }
        if (!isset($aParams["oxfiles__oxlinkexptime"]) || $aParams["oxfiles__oxlinkexptime"] == "") {
            $aParams["oxfiles__oxlinkexptime"] = -1;
        }
        if (!isset($aParams["oxfiles__oxmaxunregdownloads"]) || $aParams["oxfiles__oxmaxunregdownloads"] == "") {
            $aParams["oxfiles__oxmaxunregdownloads"] = -1;
        }
        if (!isset($aParams["oxfiles__oxmaxdownloads"]) || $aParams["oxfiles__oxmaxdownloads"] == "") {
            $aParams["oxfiles__oxmaxdownloads"] = -1;
        }
        return $aParams;
    }
}
