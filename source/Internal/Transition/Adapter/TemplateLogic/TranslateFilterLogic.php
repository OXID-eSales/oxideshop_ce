<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Exception\TranslationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\TranslatorInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Eshop\Core\Exception\StandardException;

class TranslateFilterLogic
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * TranslateFilterLogic constructor.
     * @param ContextInterface $context
     * @param TranslatorInterface           $translator
     */
    public function __construct(ContextInterface $context, TranslatorInterface $translator)
    {
        $this->context = $context;
        $this->translator = $translator;
    }

    /**
     * @param string $ident
     * @param mixed  $args
     *
     * @return string
     */
    public function multiLang($ident, $args = []): string
    {
        $ident = isset($ident) ? $ident : 'IDENT MISSING';

        $translation = $ident;
        $translationFound = true;

        try {
            $translation = $this->translator->translate($ident);
        } catch (TranslationNotFoundException $exception) {
            $translationFound = false;
        } catch (StandardException) {
            // is thrown in debug mode and has to be caught here!
        }

        if ($translationFound) {
            $translation = $this->assignArgumentsToTranslation($translation, $args);
        } elseif (!$this->context->isShopInProductiveMode()) {
            $translation = 'ERROR: Translation for ' . $ident . ' not found!';
        }

        return $translation;
    }

    /**
     * @param string $translation
     * @param mixed  $args
     * @return string
     */
    private function assignArgumentsToTranslation(string $translation, $args): string
    {
        if ($args) {
            if (is_array($args)) {
                $translation = vsprintf($translation, $args);
            } else {
                $translation = sprintf($translation, $args);
            }
        }
        return $translation;
    }
}
