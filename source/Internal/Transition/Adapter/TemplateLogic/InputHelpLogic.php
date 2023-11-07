<?php

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Exception\StandardException;

class InputHelpLogic
{
    /**
     * @param array $params
     *
     * @return null
     */
    public function getIdent($params)
    {
        return $params['ident'] ?? null;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getTranslation($params)
    {
        $ident = $this->getIdent($params);
        $translation = null;
        $lang = \OxidEsales\Eshop\Core\Registry::getLang();
        $tplLanguage = $lang->getTplLanguage();
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $isAdmin = $config->isAdmin();
        try {
            $translation = $lang->translateString($ident, $tplLanguage, $isAdmin);
        } catch (StandardException) {
            // is thrown in debug mode and has to be caught here!
        }

        return $translation;
    }
}
