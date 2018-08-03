<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 02.08.18
 * Time: 13:38
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

use Symfony\Component\Templating\EngineInterface;

class TemplateEngineBridge
{
    private $templateEngine;

    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function exists($name)
    {
        return $this->templateEngine->exists($name);
    }

    public function getEngine()
    {
        return $this->templateEngine;
    }

    public function renderTemplate($templateName, $viewData, $cacheId = null)
    {
        $templating = $this->templateEngine;
        $templating->setCacheId($cacheId);

        return $templating->render($templateName, $viewData);
    }
}