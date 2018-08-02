<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\TemplateController;
use OxidEsales\EshopCommunity\Core\SmartyEngine;
use oxRegistry;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * General export class.
 */
class GenericExportDo extends \OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "genExport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "genExport_main";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName = "genexport";

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "dynbase_do.tpl";

    /**
     * Does Export line by line on position iCnt
     *
     * @param integer $iCnt export position
     *
     * @return bool
     */
    public function nextTick($iCnt)
    {
        $iExportedItems = $iCnt;
        $blContinue = false;
        if ($oArticle = $this->getOneArticle($iCnt, $blContinue)) {
            $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
            $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
            $oSmarty->assign("sCustomHeader", \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("sExportCustomHeader"));
            $oSmarty->assign_by_ref("linenr", $iCnt);
            $oSmarty->assign_by_ref("article", $oArticle);
            $oSmarty->assign("spr", $myConfig->getConfigParam('sCSVSign'));
            $oSmarty->assign("encl", $myConfig->getConfigParam('sGiCsvFieldEncloser'));
            $this->write($oSmarty->fetch("genexport.tpl", $this->getViewId()));

            return ++$iExportedItems;
        }

        return $blContinue;
    }

    public function renderTemplate($templateName, $viewData, $view)
    {
        $templateNameParser = new TemplateNameParser();

        // get Smarty is important here as it sets template directory correct
        $smarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
        $smarty->oxobject = $view;
        // #2873: In demoshop for RSS we set php_handling to SMARTY_PHP_PASSTHRU
        // as SMARTY_PHP_REMOVE removes not only php tags, but also xml
        if ($this->getConfig()->isDemoShop()) {
            $smarty->php_handling = SMARTY_PHP_PASSTHRU;
        }

        $templating = new SmartyEngine($smarty, $templateNameParser);
        $templating->setCacheId($view->getViewId());

        return $templating->render($templateName, $viewData);
    }
    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     */
    public function write($sLine)
    {
        $sLine = $this->removeSID($sLine);

        $sLine = str_replace(["\r\n", "\n"], "", $sLine);
        $sLine = str_replace("<br>", "\n", $sLine);

        fwrite($this->fpFile, $sLine . "\n");
    }
}
