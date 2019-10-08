<?php

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class InputHelpLogic
{
    /**
     * @param array $params
     *
     * @return null
     */
    public function getIdent($params)
    {
        return isset($params['ident']) ? $params['ident'] : null;
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
        } catch (\OxidEsales\Eshop\Core\Exception\LanguageException $languageException) {
            // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
        }

        return $translation;
    }
}
