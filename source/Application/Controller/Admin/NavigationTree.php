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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use DOMXPath;
use DOMDocument;
use DOMElement;
use stdClass;
use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;

/**
 * Navigation tree control class
 */
class NavigationTree extends \oxSuperCfg
{
    /**
     * stores DOM object for all navigation tree
     */
    protected $_oDom = null;

    /**
     * keeps unmodified dom
     */
    protected $_oInitialDom = null;

    /**
     * Dynamix XML path
     *
     * @var string
     */
    protected $_sDynIncludeUrl = null;

    /**
     * Default EXPATH supported encodings
     *
     * @var array
     */
    protected $_aSupportedExpathXmlEncodings = array('utf-8', 'utf-16', 'iso-8859-1', 'us-ascii');

    /**
     * clean empty nodes from tree
     *
     * @param object $dom         dom object
     * @param string $parentXPath parent xpath
     * @param string $childXPath  child xpath from parent
     */
    protected function _cleanEmptyParents($dom, $parentXPath, $childXPath)
    {
        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query($parentXPath);

        foreach ($nodeList as $node) {
            $id = $node->getAttribute('id');
            $childList = $xPath->query("{$parentXPath}[@id='$id']/$childXPath");
            if (!$childList->length) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Adds links to xml nodes to resolve paths
     *
     * @param DomDocument $dom where to add links
     */
    protected function _addLinks($dom)
    {
        $url = 'index.php?'; // session parameters will be included later (after cache processor)
        $xPath = new DomXPath($dom);

        // building
        $nodeList = $xPath->query("//SUBMENU[@cl]");
        foreach ($nodeList as $node) {
            // fetching class
            $cl = $node->getAttribute('cl');
            $cl = $cl ? "cl=$cl" : '';

            // fetching params
            $param = $node->getAttribute('clparam');
            $param = $param ? "&$param" : '';

            // setting link
            $node->setAttribute('link', "{$url}{$cl}{$param}");
        }
    }

    /**
     * Loads data form XML file, and merges it with main oDomXML.
     *
     * @param string      $menuFile which file to load
     * @param DomDocument $dom      where to load
     */
    protected function _loadFromFile($menuFile, $dom)
    {
        $merge = false;
        $domFile = new DomDocument();
        $domFile->preserveWhiteSpace = false;
        if (!@$domFile->load($menuFile)) {
            $merge = true;
        } elseif (is_readable($menuFile) && ($xml = @file_get_contents($menuFile))) {
            // looking for non supported character encoding
            if (getStr()->preg_match("/encoding\=(.*)\?\>/", $xml, $matches) !== 0) {
                if (isset($matches[1])) {
                    $currEncoding = trim($matches[1], "\"");
                    if (!in_array(strtolower($currEncoding), $this->_aSupportedExpathXmlEncodings)) {
                        $xml = str_replace($matches[1], "\"UTF-8\"", $xml);
                        $xml = iconv($currEncoding, "UTF-8", $xml);
                    }
                }
            }

            // load XML as string
            if (@$domFile->loadXml($xml)) {
                $merge = true;
            }
        }

        if ($merge) {
            $this->_merge($domFile, $dom);
        }
    }

    /**
     * Adds to element DynTabs
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @param object $dom dom element to add links
     */
    protected function _addDynLinks($dom)
    {
        $myUtilsFile = oxRegistry::get("oxUtilsFile");

        $url = 'index.php?'; // session parameters will be included later (after cache processor)

        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query("//OXMENU[@type='dyn']/MAINMENU/SUBMENU");

        foreach ($nodeList as $node) {
            // fetching class
            $cl = $node->getAttribute('cl');
            $cl = "cl=dynscreen&menu=$cl";

            // fetching params
            $param = $node->getAttribute('clparam');
            $param = $param ? "&$param" : '';

            // setting list node if its is not set yet
            if (!$node->getAttribute('list')) {
                $node->setAttribute('list', 'dynscreen_list');
                $node->setAttribute('listparam', 'menu=' . $node->getAttribute('cl'));
            }

            // setting link
            $node->setAttribute('link', "{$url}{$cl}{$param}");

            // setting id
            $node->parentNode->setAttribute('id', 'dyn_menu');

            // setting id to its parent

            // fetching class
            $class = $node->getAttribute('cl');

            // always display the "about" tab no matter what licence

            if ($myUtilsFile->checkFile("{$this->_sDynIncludeUrl}pages/{$class}_about.php")) {
                $tabElem = new DOMElement('TAB');
                $node->appendChild($tabElem);
                $tabElem->setAttribute('external', 'true');
                $tabElem->setAttribute('location', "{$this->_sDynIncludeUrl}pages/{$class}_about.php");
                $tabElem->setAttribute('id', 'dyn_about');
            }

            // checking for technics page
            if ($myUtilsFile->checkFile("{$this->_sDynIncludeUrl}pages/{$class}_technics.php")) {
                $tabElem = new DOMElement('TAB');
                $node->appendChild($tabElem);
                $tabElem->setAttribute('external', 'true');
                $tabElem->setAttribute('location', "{$this->_sDynIncludeUrl}pages/{$class}_technics.php");
                $tabElem->setAttribute('id', 'dyn_interface');
            }

            // checking for setup page
            if (class_exists($class)) {
                $tabElem = new DOMElement('TAB');
                $node->appendChild($tabElem);
                $tabElem->setAttribute('id', 'dyn_interface');
                $tabElem->setAttribute('cl', $class);
            }
        }
    }

    /**
     * add session parameters to local urls
     *
     * @param object $dom dom element to add links
     */
    protected function _sessionizeLocalUrls($dom)
    {
        $url = $this->_getAdminUrl();
        $xPath = new DomXPath($dom);
        $str = getStr();
        foreach (array('url', 'link') as $attrType) {
            foreach ($xPath->query("//OXMENU//*[@$attrType]") as $node) {
                $localUrl = $node->getAttribute($attrType);
                if (strpos($localUrl, 'index.php?') === 0) {
                    $localUrl = $str->preg_replace('#^index.php\?#', $url, $localUrl);
                    $node->setAttribute($attrType, $localUrl);
                }
            }
        }
    }

    /**
     * Removes form tree elements which does not have required user rights
     *
     * @param object $dom DOMDocument
     */
    protected function _checkRights($dom)
    {
        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query('//*[@rights or @norights]');

        foreach ($nodeList as $node) {
            // only allowed modules/user rights or so
            if (($req = $node->getAttribute('rights'))) {
                $perms = explode(',', $req);
                foreach ($perms as $perm) {
                    if ($perm && !$this->_hasRights($perm)) {
                        $node->parentNode->removeChild($node);
                    }
                }
                // not allowed modules/user rights or so
            } elseif (($noReq = $node->getAttribute('norights'))) {
                $perms = explode(',', $noReq);
                foreach ($perms as $perm) {
                    if ($perm && $this->_hasRights($perm)) {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
        }
    }

    /**
     * Removes from tree elements which don't have required groups
     *
     * @param DOMDocument $dom document to check group
     */
    protected function _checkGroups($dom)
    {
        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query("//*[@nogroup or @group]");

        foreach ($nodeList as $node) {
            // allowed only for groups
            if (($req = $node->getAttribute('group'))) {
                $perms = explode(',', $req);
                foreach ($perms as $perm) {
                    if ($perm && !$this->_hasGroup($perm)) {
                        $node->parentNode->removeChild($node);
                    }
                }
                // not allowed for groups
            } elseif (($noReq = $node->getAttribute('nogroup'))) {
                $perms = explode(',', $noReq);
                foreach ($perms as $perm) {
                    if ($perm && $this->_hasGroup($perm)) {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
        }
    }

    /**
     * Removes form tree elements if this is demo shop and elements have disableForDemoShop="1"
     *
     * @param DOMDocument $dom document to check group
     *
     * @return null
     */
    protected function _checkDemoShopDenials($dom)
    {
        if (!$this->getConfig()->isDemoShop()) {
            // nothing to check for non demo shop
            return;
        }

        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query("//*[@disableForDemoShop]");
        foreach ($nodeList as $node) {
            if ($node->getAttribute('disableForDemoShop')) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Removes node from tree elements if it is marked as not visible (visible="0")
     *
     * @param DOMDocument $dom document to check group
     *
     * @return null
     */
    protected function removeInvisibleMenuNodes($dom)
    {
        $xPath = new DomXPath($dom);
        $nodeList = $xPath->query("//*[@visible]");
        foreach ($nodeList as $node) {
            if (!$node->getAttribute('visible')) {
                $node->parentNode->removeChild($node);
            }
        }
    }

    /**
     * Copys attributes form one element to another
     *
     * @param object $domElemTo   DOMElement
     * @param object $domElemFrom DOMElement
     */
    protected function _copyAttributes($domElemTo, $domElemFrom)
    {
        foreach ($domElemFrom->attributes as $attr) {
            $domElemTo->setAttribute($attr->nodeName, $attr->nodeValue);
        }
    }

    /**
     * Merges nodes of newly added menu xml file
     *
     * @param object $domElemTo   merge target
     * @param object $domElemFrom merge source
     * @param object $xPathTo     node path
     * @param object $domDocTo    node to append child
     * @param string $queryStart  node query
     */
    protected function _mergeNodes($domElemTo, $domElemFrom, $xPathTo, $domDocTo, $queryStart)
    {
        foreach ($domElemFrom->childNodes as $fromNode) {
            if ($fromNode->nodeType === XML_ELEMENT_NODE) {
                $fromAttrName = $fromNode->getAttribute('id');
                $fromNodeName = $fromNode->tagName;

                // find current item
                $query = "{$queryStart}/{$fromNodeName}[@id='{$fromAttrName}']";
                $curNode = $xPathTo->query($query);

                // if not found - append
                if ($curNode->length == 0) {
                    $domElemTo->appendChild($domDocTo->importNode($fromNode, true));
                } else {
                    $curNode = $curNode->item(0);

                    // if found copy all attributes and check childnodes
                    $this->_copyAttributes($curNode, $fromNode);

                    if ($fromNode->childNodes->length) {
                        $this->_mergeNodes($curNode, $fromNode, $xPathTo, $domDocTo, $query);
                    }
                }
            }
        }
    }

    /**
     * If oDomXML exist meges nodes
     *
     * @param DomDocument $domNew what to merge
     * @param DomDocument $dom    where to merge
     */
    protected function _merge($domNew, $dom)
    {
        $xPath = new DOMXPath($dom);
        $this->_mergeNodes($dom->documentElement, $domNew->documentElement, $xPath, $dom, '/OX');
    }

    /**
     * Returns from oDomXML tree tabs DOMNodeList, which belongs to $id
     *
     * @param string $id        class name
     * @param int    $act       current tab number
     * @param bool   $setActive marks tab as active
     *
     * @return DOMNodeList
     */
    public function getTabs($id, $act, $setActive = true)
    {
        $xPath = new DOMXPath($this->getDomXml());
        //$nodeList = $xPath->query( "//SUBMENU[@cl='$id' or @list='$id']/TAB | //SUBMENU/../TAB[@cl='$id']" );
        $nodeList = $xPath->query("//SUBMENU[@cl='$id']/TAB | //SUBMENU[@list='$id']/TAB | //SUBMENU/../TAB[@cl='$id']");

        $act = ($act > $nodeList->length) ? ($nodeList->length - 1) : $act;

        if ($setActive) {
            foreach ($nodeList as $pos => $node) {
                if ($pos == $act) {
                    // marking active node
                    $node->setAttribute('active', 1);
                }
            }
        }

        return $nodeList;
    }

    /**
     * Returns active TAB class name
     *
     * @param string $id  class name
     * @param int    $act active tab number
     *
     * @return string
     */
    public function getActiveTab($id, $act)
    {
        $nodeList = $this->getTabs($id, $act, false);
        $act = ($act > $nodeList->length) ? ($nodeList->length - 1) : $act;
        if ($nodeList->length && ($node = $nodeList->item($act))) {
            return $node->getAttribute('cl');
        }
    }

    /**
     * returns from oDomXML tree buttons stdClass, which belongs to $class
     *
     * @param string $class class name
     *
     * @return mixed
     */
    public function getBtn($class)
    {
        $buttons = null;
        $xPath = new DOMXPath($this->getDomXml());
        $nodeList = $xPath->query("//TAB[@cl='$class']/../BTN");
        if ($nodeList->length) {
            $buttons = new stdClass();
            foreach ($nodeList as $node) {
                $btnId = $node->getAttribute('id');
                $buttons->$btnId = 1;
            }
        }

        return $buttons;
    }

    /**
     * Returns array witn pathes + names ox manu xml files. Paths are checked
     *
     * @return array
     */
    protected function _getMenuFiles()
    {
        $myConfig = $this->getConfig();
        $myOxUtlis = oxRegistry::getUtils();

        $editionPathSelector = new EditionPathProvider(new EditionRootPathProvider(new EditionSelector()));
        $fullAdminDir = $editionPathSelector->getViewsDirectory() . 'admin' . DIRECTORY_SEPARATOR;
        $menuFile = $fullAdminDir . 'menu.xml';

        $tmpDir = $myConfig->getConfigParam('sCompileDir');
        $dynLang = $this->_getDynMenuLang();
        $localDynPath = "{$tmpDir}{$dynLang}_dynscreen.xml";

        // including std file
        if (file_exists($menuFile)) {
            $filesToLoad[] = $menuFile;
        }

        // including custom file
        if (file_exists($fullAdminDir . 'user.xml')) {
            $filesToLoad[] = $fullAdminDir . 'user.xml';
        }

        // including module menu files
        $path = getShopBasePath();
        $modulelist = oxNew('oxmodulelist');
        $activeModuleInfo = $modulelist->getActiveModuleInfo();
        if (is_array($activeModuleInfo)) {
            foreach ($activeModuleInfo as $modulePath) {
                $fullPath = $path . "modules/" . $modulePath;
                // missing file/folder?
                if (is_dir($fullPath)) {
                    // including menu file
                    $menuFile = $fullPath . "/menu.xml";
                    if (file_exists($menuFile) && is_readable($menuFile)) {
                        $filesToLoad[] = $menuFile;
                    }
                }
            }
        }

        $loadDynContents = $myConfig->getConfigParam('blLoadDynContents');
        $shopCountry = $myConfig->getConfigParam('sShopCountry');

        // including dyn menu file
        $dynPath = null;

        if ($loadDynContents) {
            if ($shopCountry) {
                $remoteDynUrl = $this->_getDynMenuUrl($dynLang, $loadDynContents);

                // loading remote file from server only once
                $loadRemote = oxRegistry::getSession()->getVariable("loadedremotexml");

                // very basic check if its valid xml file
                if ((!isset($loadRemote) || $loadRemote) && ($dynPath = $myOxUtlis->getRemoteCachePath($remoteDynUrl, $localDynPath))) {
                    $dynPath = $this->_checkDynFile($dynPath);
                }

                // caching last load state
                oxRegistry::getSession()->setVariable("loadedremotexml", $dynPath ? true : false);
            }
        }

        // loading dynpages
        if ($dynPath) {
            // for not loading/showing the dynpages, we set don't add the dyn path to the files to load:
            // $filesToLoad[] = $dynPath;
        }

        return $filesToLoad;
    }

    /**
     * Checks if dyn file is valid for inclusion
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @param string $dynFilePath dyn file path
     *
     * @return bool
     */
    protected function _checkDynFile($dynFilePath)
    {
        $dynFile = null;
        if (file_exists($dynFilePath)) {
            $line = null;
            if (($handle = @fopen($dynFilePath, 'r'))) {
                $line = stream_get_line($handle, 100, "?>");
                fclose($handle);

                // checking xml file header
                if ($line && stripos($line, '<?xml') !== false) {
                    $dynFile = $dynFilePath;
                }
            }

            // cleanup ..
            if (!$dynFile) {
                @unlink($dynFilePath);
            }
        }

        return $dynFile;
    }

    /**
     * Method is used for overriding.
     *
     * @param string $cacheContents
     *
     * @return string
     */
    protected function _processCachedFile($cacheContents)
    {
        return $cacheContents;
    }

    /**
     * get initial dom, not modified by init method
     *
     * @return DOMDocument
     */
    protected function _getInitialDom()
    {
        if ($this->_oInitialDom === null) {
            $myOxUtlis = oxRegistry::getUtils();

            if (is_array($filesToLoad = $this->_getMenuFiles())) {
                // now checking if xml files are newer than cached file
                $reload = false;
                $dynLang = $this->_getDynMenuLang();

                $shopId = $this->getConfig()->getActiveShop()->getShopId();
                $cacheName = 'menu_' . $dynLang . $shopId . '_xml';
                $cacheFile = $myOxUtlis->getCacheFilePath($cacheName);
                $cacheContents = $myOxUtlis->fromFileCache($cacheName);
                if ($cacheContents && file_exists($cacheFile) && ($cacheModTime = filemtime($cacheFile))) {
                    foreach ($filesToLoad as $dynPath) {
                        if ($cacheModTime < filemtime($dynPath)) {
                            $reload = true;
                        }
                    }
                } else {
                    $reload = true;
                }

                $this->_oInitialDom = new DOMDocument();
                if ($reload) {
                    // fully reloading and building pathes
                    $this->_oInitialDom->appendChild(new DOMElement('OX'));

                    foreach ($filesToLoad as $dynPath) {
                        $this->_loadFromFile($dynPath, $this->_oInitialDom);
                    }

                    // adds links to menu items
                    $this->_addLinks($this->_oInitialDom);

                    // @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
                    // adds links to dynamic parts
                    $this->_addDynLinks($this->_oInitialDom);
                    // END deprecated

                    // writing to cache
                    $myOxUtlis->toFileCache($cacheName, $this->_oInitialDom->saveXML());
                } else {
                    $cacheContents = $this->_processCachedFile($cacheContents);
                    // loading from cached file
                    $this->_oInitialDom->preserveWhiteSpace = false;
                    $this->_oInitialDom->loadXML($cacheContents);
                }

                // add session params
                $this->_sessionizeLocalUrls($this->_oInitialDom);
            }
        }

        return $this->_oInitialDom;
    }

    /**
     * Returns DomXML
     *
     * @return DOMDocument
     */
    public function getDomXml()
    {
        if ($this->_oDom === null) {
            $this->_oDom = clone $this->_getInitialDom();

            // removes items denied by user group
            $this->_checkGroups($this->_oDom);

            // removes items denied by user rights
            $this->_checkRights($this->_oDom);

            // removes items marked as not visible
            $this->removeInvisibleMenuNodes($this->_oDom);

            // check config params
            $this->_checkDemoShopDenials($this->_oDom);
            $this->onGettingDomXml();
            $this->_cleanEmptyParents($this->_oDom, '//SUBMENU[@id][@list]', 'TAB');
            $this->_cleanEmptyParents($this->_oDom, '//MAINMENU[@id]', 'SUBMENU');
        }

        return $this->_oDom;
    }

    /**
     * Returns DOMNodeList of given navigation classes
     *
     * @param array $nodes Node array
     *
     * @return DOMNodeList
     */
    public function getListNodes($nodes)
    {
        $xPath = new DOMXPath($this->getDomXml());
        $nodeList = $xPath->query("//SUBMENU[@cl='" . implode("' or @cl='", $nodes) . "']");

        return ($nodeList->length) ? $nodeList : null;
    }

    /**
     * Marks passed node as active
     *
     * @param string $nodeId node id
     */
    public function markNodeActive($nodeId)
    {
        $xPath = new DOMXPath($this->getDomXml());
        $nodeList = $xPath->query("//*[@cl='{$nodeId}' or @list='{$nodeId}']");

        if ($nodeList->length) {
            foreach ($nodeList as $node) {
                // special case for external resources
                $node->setAttribute('active', 1);
                $node->parentNode->setAttribute('active', 1);
            }
        }

    }

    /**
     * Formats and returns url for list area
     *
     * @param string $id tab related class
     *
     * @return string
     */
    public function getListUrl($id)
    {
        $xPath = new DOMXPath($this->getDomXml());
        $nodeList = $xPath->query("//SUBMENU[@cl='{$id}']");
        if ($nodeList->length && ($node = $nodeList->item(0))) {
            $cl = $node->getAttribute('list');
            $cl = $cl ? "cl=$cl" : '';

            $params = $node->getAttribute('listparam');
            $params = $params ? "&$params" : '';

            return "{$cl}{$params}";
        }
    }

    /**
     * Formats and returns url for edit area
     *
     * @param string $id     tab related class
     * @param int    $actTab active tab
     *
     * @return string
     */
    public function getEditUrl($id, $actTab)
    {
        $xPath = new DOMXPath($this->getDomXml());
        $nodeList = $xPath->query("//SUBMENU[@cl='{$id}']/TAB");

        $actTab = ($actTab > $nodeList->length) ? ($nodeList->length - 1) : $actTab;
        if ($nodeList->length && ($actTab = $nodeList->item($actTab))) {
            // special case for external resources
            if ($actTab->getAttribute('external')) {
                return $actTab->getAttribute('location');
            }
            $cl = $actTab->getAttribute('cl');
            $cl = $cl ? "cl={$cl}" : '';

            $params = $actTab->getAttribute('clparam');
            $params = $params ? "&{$params}" : '';

            return "{$cl}{$params}";
        }
    }

    /**
     * Admin url getter
     *
     * @return string
     */
    protected function _getAdminUrl()
    {
        $myConfig = $this->getConfig();

        if (($adminSslUrl = $myConfig->getConfigParam('sAdminSSLURL'))) {
            $url = trim($adminSslUrl, '/');
        } else {
            $url = trim($myConfig->getConfigParam('sShopURL'), '/') . '/admin';
        }

        return oxRegistry::get("oxUtilsUrl")->processUrl("{$url}/index.php", false);
    }

    /**
     * Checks if user has required rights
     *
     * @param string $rights session user rights
     *
     * @return bool
     */
    protected function _hasRights($rights)
    {
        return $this->getUser()->oxuser__oxrights->value == $rights;
    }

    /**
     * Checks if user in required group
     *
     * @param string $groupId active group id
     *
     * @return bool
     */
    protected function _hasGroup($groupId)
    {
        return $this->getUser()->inGroup($groupId);
    }

    /**
     * Returns id of class assigned to current node
     *
     * @param string $className active class name
     *
     * @return string
     */
    public function getClassId($className)
    {
        $xPath = new DOMXPath($this->_getInitialDom());
        $nodeList = $xPath->query("//*[@cl='{$className}' or @list='{$className}']");
        if ($nodeList->length && ($firstItem = $nodeList->item(0))) {
            return $firstItem->getAttribute('id');
        }
    }


    /**
     * Get dynamic pages url or local path
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @param int    $lang             language id
     * @param string $loadDynContents get local or remote content path
     *
     * @return string
     */
    protected function _getDynMenuUrl($lang, $loadDynContents)
    {
        if (!$loadDynContents) {
            // getting dyn info from oxid server is off, so getting local menu path
            $fullAdminDir = getShopBasePath() . 'Application/views/admin';

            return $fullAdminDir . "/dynscreen_local.xml";
        }
        $adminView = oxNew('oxadminview');
        $this->_sDynIncludeUrl = $adminView->getServiceUrl($lang);

        return $this->_sDynIncludeUrl . "menue/dynscreen.xml";
    }

    /**
     * Get dynamic pages language code
     *
     * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
     *
     * @return string
     */
    protected function _getDynMenuLang()
    {
        $myConfig = $this->getConfig();
        $lang = oxRegistry::getLang();

        $dynLang = $myConfig->getConfigParam('iDynInterfaceLanguage');
        $dynLang = isset($dynLang) ? $dynLang : ($lang->getTplLanguage());

        return $lang->getLanguageArray()[$dynLang]->abbr;
    }

    /**
     * Method is used for overriding.
     */
    protected function onGettingDomXml()
    {
    }
}
