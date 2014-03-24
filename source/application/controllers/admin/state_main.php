<?php

/**
 * Admin master settings main states manager.
 * There is possibility to change state title,
 * and etc.
 * Admin Menu: Master Settings -> States --> Main.
 * @package admin
 */
class state_main extends oxAdminDetails
{
    /**
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            /** @var oxstate $oState */
            $oState = oxNew( "oxstate" );
            $oState->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oState->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oState->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oState;

            // remove already created languages
            $aLang = array_diff (oxRegistry::getLang()->getLanguageNames(), $oOtherLang );
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }
        return "state_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter( "editval" );

        /** @var oxstate $oState */
        $oState = oxNew("oxstate");

        if ( $soxId != "-1") {
            $oState->loadInLang( $this->_iEditLang, $soxId );
        } else {
            $aParams['oxstates__oxid']        = null;
        }

        $oState->setLanguage(0);
        $oState->assign( $aParams );
        $oState->setLanguage($this->_iEditLang);
        $oState->save();

        // set oxid if inserted
        $this->setEditObjectId( $oState->getId() );
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter( "editval");

        /** @var oxstate $oState */
        $oState = oxNew( "oxstate" );

        if ($soxId != "-1") {
            $oState->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxstates__oxid'] = null;
        }
        $oState->setLanguage(0);
        $oState->assign( $aParams);
        $oState->setLanguage($this->_iEditLang);

        $oState->save();

        // set oxid if inserted
        $this->setEditObjectId( $oState->getId() );
    }

    /**
     * @return oxCountryList
     */
    public function getCountryList()
    {
        /** @var oxCountryList $oCountryList */
        $oCountryList = oxNew("oxCountryList");
        $oCountryList->loadList();
        return $oCountryList;
    }
}