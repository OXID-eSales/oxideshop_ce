<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Exception\TranslationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator\TranslatorInterface;
use OxidEsales\Eshop\Core\Exception\StandardException;

class TranslateSalutationLogic
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TranslateSalutationLogic constructor.
     * @param TranslatorInterface           $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $ident
     *
     * @return string
     */
    public function translateSalutation(string $ident = ''): string
    {
        $translation = $ident;
        try {
            $translation = $this->translator->translate($ident);
        } catch (TranslationNotFoundException) {
            // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
        }

        return $translation;
    }
}
