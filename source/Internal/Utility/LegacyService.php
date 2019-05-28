<?php


namespace OxidEsales\EshopCommunity\Internal\Utility;


use OxidEsales\EshopCommunity\Core\Registry;

class LegacyService implements LegacyServiceInterface
{
    public function getUniqueId() {
        return Registry::getUtilsObject()->generateUId();
    }

}