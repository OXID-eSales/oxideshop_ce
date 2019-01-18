<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Decorator for
 */
class AdminLogSqlDecorator
{
    protected $table = 'oxadminlog';

    /**
     * Injects argument to admin log insert sql.
     *
     * @param string $originalSql
     * @return string
     */
    public function prepareSqlForLogging($originalSql)
    {
        $userId = $this->getUserId();

        return "insert into {$this->table} (oxuserid, oxsql) values ('{$userId}', " . $this->quote($originalSql) . ")";
    }

    /**
     * Get currently logged in admin user id
     *
     * @return string
     */
    protected function getUserId()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($user->loadAdminUser()) {
            return $user->getId();
        }
    }

    /**
     * Quotes the string for saving in database field;
     *
     * @param string $str
     *
     * @return string
     */
    protected function quote($str)
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($str);
    }
}
