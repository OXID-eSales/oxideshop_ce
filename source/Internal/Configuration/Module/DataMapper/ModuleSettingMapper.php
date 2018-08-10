<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;


use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;

/**
 * Class ModuleSettingMapper
 *
 * @package OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper
 */
class ModuleSettingMapper
{

    /**
     * @param $object
     *
     * @return array
     */
    public function toData($object): array
    {
        return [];
    }

    /**
     * @param array $data
     *
     * @return mixed|ModuleSetting
     */
    public function fromData(array $data)
    {
        return new ModuleSetting();
    }
}