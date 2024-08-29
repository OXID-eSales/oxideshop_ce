<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Exception\TranslationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\TranslatorInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\Eshop\Core\Exception\StandardException;

use function is_array;

class TranslateFunctionLogic
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
     * TranslateFunctionLogic constructor.
     * @param ContextInterface $context
     * @param TranslatorInterface           $translator
     */
    public function __construct(ContextInterface $context, TranslatorInterface $translator)
    {
        $this->context = $context;
        $this->translator = $translator;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getTranslation(array $params): string
    {
        $ident = $params['ident'] ?? 'IDENT MISSING';
        $suffix = $params['suffix'] ?? 'NO_SUFFIX';
        $translation = $ident;
        $suffixTranslation = $suffix;
        $translationFound = true;

        try {
            $translation = $this->translator->translate($ident);
            if ($this->isTranslatableSuffix($suffix)) {
                $suffixTranslation = $this->translator->translate($suffix);
            }
        } catch (TranslationNotFoundException $exception) {
            $translationFound = false;
        } catch (StandardException) {
            // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
        }

        if (!$translationFound && isset($params['alternative'])) {
            $translation = $params['alternative'];
            $translationFound = true;
        }
        if ($translationFound) {
            $translation = $this->assignArgumentsToTranslation($translation, $params);
            if ($this->isTranslatableSuffix($suffix)) {
                $translation .= $suffixTranslation;
            }
        } elseif ($this->showError($params)) {
            $translation = sprintf(
                'ERROR: Translation for %s%s not found!',
                $ident,
                $this->isTranslatableSuffix($suffixTranslation) ? $suffixTranslation : ''
            );
        } else {
            Registry::getLogger()->warning(
                "translation for $ident not found"
            );
        }

        return $translation;
    }

    private function isTranslatableSuffix(string $suffix): bool
    {
        return !empty($suffix) && $suffix !== 'NO_SUFFIX';
    }

    private function assignArgumentsToTranslation(string $translation, array $params): string
    {
        if (isset($params['args']) && $params['args'] !== false) {
            $translation = is_array($params['args']) ?
                vsprintf($translation, $params['args']) :
                sprintf($translation, $params['args']);
        }
        return $translation;
    }

    /**
     * @param array $params
     * @return bool
     */
    private function showError(array $params): bool
    {
        $showError = isset($params['noerror']) ? !$params['noerror'] : true;
        if (!$this->context->isAdmin() && $this->context->isShopInProductiveMode()) {
            $showError = false;
        }
        return $showError;
    }
}
