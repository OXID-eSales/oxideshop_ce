<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 02.08.18
 * Time: 13:38
 */

namespace OxidEsales\EshopCommunity\Core;


use Symfony\Component\Templating\TemplateNameParser;

class TemplateRenderer
{
    public function renderTemplate($templateName, $viewData, $view)
    {
        $templateNameParser = new TemplateNameParser();

        // get Smarty is important here as it sets template directory correct
        $smarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
        $smarty->oxobject = $view;
        $templating = new SmartyEngine($smarty, $templateNameParser);
        $templating->setCacheId($view->getViewId());

        return $templating->render($templateName, $viewData);
    }
}