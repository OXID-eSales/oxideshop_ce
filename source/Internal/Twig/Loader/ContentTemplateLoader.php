<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Loader;

use OxidEsales\EshopCommunity\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Twig\Exception\InvalidTemplateNameException;
use OxidEsales\EshopCommunity\Internal\Twig\TemplateLoaderNameParser;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Class ContentSnippetLoader
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class ContentTemplateLoader implements LoaderInterface
{

    /** @var Content */
    private $content;

    /** @var string */
    private $name;

    /** @var TemplateLoaderNameParser */
    private $nameParser;

    /**
     * ContentTemplateLoader constructor.
     *
     * @param TemplateLoaderNameParser $nameParser
     */
    public function __construct(TemplateLoaderNameParser $nameParser)
    {
        $this->nameParser = $nameParser;
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @param string $name The template logical name
     *
     * @return Source
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext($name)
    {
        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);
        $parameters = $this->nameParser->getParameters($name);

        $content = $this->getContent($name);

        if ($content && $content->oxcontents__oxactive->value) {
            $field = "oxcontent";
            if (isset($parameters['field'])) {
                $field = $parameters['field'];
            }

            $property = 'oxcontents__' . $field;
            $code = clone $content->$property;
        } else {
            throw new LoaderError("Template with $key '$value' not found.");
        }

        return new Source($code, $name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey($name)
    {
        $content = $this->getContent($name);

        $cacheKey = sprintf("%s(%s)", $name, $content->getLanguage());

        return $cacheKey;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param int    $time Timestamp of the last modification time of the cached template
     *
     * @return bool true if the template is fresh, false otherwise
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh($name, $time)
    {
        $contentTime = strtotime($this->getContent($name)->oxcontents__oxtimestamp->value);

        return $time > $contentTime;
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool If the template source code is handled by this loader or not
     */
    public function exists($name)
    {
        if (!$this->nameParser->isValidName($name)) {
            throw new InvalidTemplateNameException();
        }

        $loaderName = $this->nameParser->getLoaderName($name);
        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);
        $content = $this->getContent($name);

        return $loaderName == 'contentSnippet' && in_array($key, ['ident', 'oxid']) && $value && $content;
    }

    /**
     * @param string $name
     *
     * @return Content
     *
     * @throws LoaderError
     */
    private function getContent($name)
    {
        if (!$this->nameParser->isValidName($name)) {
            throw new InvalidTemplateNameException();
        }

        if ($name == $this->name) {
            return $this->content;
        }

        $this->content = null;
        $this->name = $name;
        $this->content;

        $key = $this->nameParser->getKey($name);
        $value = $this->nameParser->getValue($name);

        $content = oxNew("oxcontent");

        if ($key == 'ident') {
            $isLoaded = $content->loadbyIdent($value);
        } elseif ($key == 'oxid') {
            $isLoaded = $content->load($value);
        } else {
            $this->name = null;
            throw new LoaderError("Cannot load template. Not provided neither ident nor oxid.");
        }

        if (!$content->oxcontents__oxactive->value) {
            $this->name = null;
            throw new LoaderError("Cannot load template. It's not active.");
        }

        if ($isLoaded) {
            $this->content = $content;
        }

        return $this->content;
    }
}
