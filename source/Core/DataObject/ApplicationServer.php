<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\DataObject;

/**
 * Class used as entity for server node information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class ApplicationServer
{
    /**
     * Time in seconds, active server information life time.
     */
    const SERVER_INFORMATION_TIME_LIFE = 86400;

    /**
     * Time in seconds, how long inactive server information will be stored.
     */
    const INACTIVE_SERVER_STORAGE_PERIOD = 259200;

    /**
     * Time in seconds, how often server information must be updated.
     */
    const SERVER_INFO_UPDATE_PERIOD = 86400;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var int
     */
    private $timestamp;

    /**
     * Flag which stores timestamp.
     *
     * @var int
     */
    private $lastFrontendUsage;

    /**
     * Flag which stores timestamp.
     *
     * @var int
     */
    private $lastAdminUsage;

    /**
     * Sets id.
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ip.
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Gets ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sets timestamp.
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Gets timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets last admin usage.
     *
     * @param int|null $lastAdminUsage
     */
    public function setLastAdminUsage($lastAdminUsage)
    {
        $this->lastAdminUsage = $lastAdminUsage;
    }

    /**
     * Gets last admin usage.
     *
     * @return int|null
     */
    public function getLastAdminUsage()
    {
        return $this->lastAdminUsage;
    }

    /**
     * Sets last frontend usage.
     *
     * @param int|null $lastFrontendUsage Admin server flag which stores timestamp.
     */
    public function setLastFrontendUsage($lastFrontendUsage)
    {
        $this->lastFrontendUsage = $lastFrontendUsage;
    }

    /**
     * Gets last frontend usage.
     *
     * @return int|null Frontend server flag which stores timestamp.
     */
    public function getLastFrontendUsage()
    {
        return $this->lastFrontendUsage;
    }

    /**
     * Check if application server was in use during 24h period.
     *
     * @param int $currentTimestamp The current timestamp.
     *
     * @return bool
     */
    public function isInUse($currentTimestamp)
    {
        return !$this->hasLifetimeExpired($currentTimestamp, self::SERVER_INFORMATION_TIME_LIFE);
    }

    /**
     * Check if application server availability check period is over.
     *
     * @param int $currentTimestamp The current timestamp.
     *
     * @return bool
     */
    public function needToDelete($currentTimestamp)
    {
        return $this->hasLifetimeExpired($currentTimestamp, self::INACTIVE_SERVER_STORAGE_PERIOD);
    }

    /**
     * Check if application server information must be updated.
     *
     * @param int $currentTimestamp The current timestamp.
     *
     * @return bool
     */
    public function needToUpdate($currentTimestamp)
    {
        return ($this->hasLifetimeExpired($currentTimestamp, self::SERVER_INFO_UPDATE_PERIOD)
            || !$this->isServerTimeValid($currentTimestamp));
    }

    /**
     * Method checks if server time was not rolled back.
     *
     * @param int $currentTimestamp The current timestamp.
     *
     * @return bool
     */
    private function isServerTimeValid($currentTimestamp)
    {
        $timestamp = $this->getTimestamp();
        return ($currentTimestamp - $timestamp) >= 0;
    }

    /**
     * Compare if the application server lifetime has exceeded given period.
     *
     * @param int $currentTimestamp The current timestamp.
     * @param int $periodTimestamp  The timestamp of period to check.
     *
     * @return bool
     */
    private function hasLifetimeExpired($currentTimestamp, $periodTimestamp)
    {
        $timestamp = $this->getTimestamp();
        return (bool) ($currentTimestamp - $timestamp >= $periodTimestamp);
    }
}
