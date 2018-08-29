<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\GenericImport\ImportObject;

use Exception;

/**
 * Import object for Users.
 */
class User extends \OxidEsales\Eshop\Core\GenericImport\ImportObject\ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxuser';

    /** @var string Shop object name. */
    protected $shopObjectName = 'oxuser';

    /**
     * Imports user. Returns import status.
     *
     * @param array $data db row array
     *
     * @throws Exception If user exists with provided OXID, throw an exception.
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    public function import($data)
    {
        if (isset($data['OXUSERNAME'])) {
            $id = $data['OXID'];
            $userName = $data['OXUSERNAME'];

            $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class, "core");
            $user->oxuser__oxusername = new \OxidEsales\Eshop\Core\Field($userName, \OxidEsales\Eshop\Core\Field::T_RAW);

            if ($user->exists($id) && $id != $user->getId()) {
                throw new Exception("USER $userName already exists!");
            }
        }

        return parent::import($data);
    }
}
