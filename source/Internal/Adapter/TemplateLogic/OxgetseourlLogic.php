<?php

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

/**
 * Class OxgetseourlLogic
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class OxgetseourlLogic
{
    /** @var \Smarty */
    private $smarty;

    /**
     * OxgetseourlLogic constructor.
     *
     * @param \Smarty $smarty
     */
    public function __construct(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * @param $params
     *
     * @return null|string
     */
    public function oxgetseourl($params)
    {
        $smarty = $this->smarty;

        $sOxid = isset( $params['oxid'] ) ? $params['oxid'] : null;
        $sType = isset( $params['type'] ) ? $params['type'] : null;
        $sUrl  = $sIdent = isset( $params['ident'] ) ? $params['ident'] : null;

        // requesting specified object SEO url
        if ( $sType ) {
            $oObject = oxNew( $sType );

            // special case for content type object when ident is provided
            if ( $sType == 'oxcontent' && $sIdent && $oObject->loadByIdent( $sIdent ) ) {
                $sUrl = $oObject->getLink();
            } elseif ( $sOxid ) {
                //minimising aricle object loading
                if ( strtolower($sType) == "oxarticle") {
                    $oObject->disablePriceLoad();
                    $oObject->setNoVariantLoading( true );
                }

                if ( $oObject->load( $sOxid ) ) {
                    $sUrl = $oObject->getLink();
                }
            }
        } elseif ( $sUrl && \OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive() ) {
            // if SEO is on ..
            $oEncoder = \OxidEsales\Eshop\Core\Registry::getSeoEncoder();
            if ( ( $sStaticUrl = $oEncoder->getStaticUrl( $sUrl ) ) ) {
                $sUrl = $sStaticUrl;
            } else {
                // in case language parameter is not added to url
                $sUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl( $sUrl );
            }
        }

        $sDynParams = isset( $params['params'] )?$params['params']:false;
        if ( $sDynParams ) {
            include_once $smarty->_get_plugin_filepath( 'modifier', 'oxaddparams' );
            $sUrl = smarty_modifier_oxaddparams( $sUrl, $sDynParams );
        }

        return $sUrl;
    }
}