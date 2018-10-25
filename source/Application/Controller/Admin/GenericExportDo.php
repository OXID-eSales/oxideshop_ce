<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\TemplateController;
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
            $parameters = [
                "sCustomHeader" => \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("sExportCustomHeader"),
                "linenr"        => $iCnt,
                "article"       => $oArticle,
                "spr"           => $myConfig->getConfigParam('sCSVSign'),
                "encl"          => $myConfig->getConfigParam('sGiCsvFieldEncloser')
            ];

            $template = $this->getTemplating();
            $this->write($template->renderTemplate("genexport.tpl", $parameters, $this->getViewId()));

            return ++$iExportedItems;
        }

        return $blContinue;
    }

    protected function getTemplating()
    {
        return $this->getContainer()->get(\OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineBridgeInterface::class);
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
