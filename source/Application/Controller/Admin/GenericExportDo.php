<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

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
    protected $_sThisTemplate = "dynbase_do";

    /**
     * Does Export line by line on position iCnt
     *
     * @param integer $cnt export position
     *
     * @return bool
     */
    public function nextTick($cnt)
    {
        $exportedItems = $cnt;
        $continue = false;
        if ($article = $this->getOneArticle($cnt, $continue)) {
            $config = Registry::getConfig();
            $article->longDescription = $this->prepareLongDescription($article);
            $context = [
                "sCustomHeader" => Registry::getSession()->getVariable("sExportCustomHeader"),
                "linenr"        => $cnt,
                "article"       => $article,
                "spr"           => $config->getConfigParam('sCSVSign'),
                "encl"          => $config->getConfigParam('sGiCsvFieldEncloser')
            ];
            $context['oxEngineTemplateId'] = $this->getViewId();

            $this->write(
                $this->getRenderer()->renderTemplate(
                    "genexport",
                    $context
                )
            );

            return ++$exportedItems;
        }

        return $continue;
    }

    private function prepareLongDescription(Article $article): string
    {
        if ($article->getLongDescription() && $article->getLongDescription()->getRawValue()) {
            $activeLanguageId = Registry::getLang()->getTplLanguage();
            $oxid = (string) $article->getId() . (string) $article->getLanguage();
            return $this->getRenderer()->renderFragment(
                $article->getLongDescription()->getRawValue(),
                "ox:{$oxid}{$activeLanguageId}",
                $this->getViewData()
            );
        }
        return '';
    }

    private function getRenderer(): TemplateRendererInterface
    {
        return $this
            ->getService(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
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

    /**
     * Current view ID getter helps to identify navigation position.
     * Bypassing dynexportbase::getViewId
     *
     * @return string
     */
    public function getViewId()
    {
        return \OxidEsales\Eshop\Application\Controller\Admin\AdminController::getViewId();
    }
}
