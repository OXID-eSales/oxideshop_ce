<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\OnlineInfo;

use OxidEsales\Eshop\Core\Dao\ApplicationServerDao;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\OnlineLicenseCheck;
use OxidEsales\Eshop\Core\OnlineLicenseCheckCaller;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Service\ApplicationServerExporter;
use OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface;
use OxidEsales\Eshop\Core\Service\ApplicationServerService;
use OxidEsales\Eshop\Core\ShopVersion;
use OxidEsales\Eshop\Core\SimpleXml;
use OxidEsales\Eshop\Core\UserCounter;
use OxidEsales\Eshop\Core\UtilsDate;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\Facts\Facts;
use SimpleXMLElement;

final class OnlineLicenseCheckRequestFormationTest extends IntegrationTestCase
{
    use ContainerTrait;

    private int $adminUserCount;

    private int $timestamp;

    private string $clusterId;

    private string $documentName = 'olcRequest';

    private string $edition;

    private string $licenseKeyExisting;

    private string $licenseKeyNew;

    private string $pVersion = '1.1';

    private string $productId = 'eShop';

    private string $serverId = 'server_id1';

    private string $serverIp = '127.0.0.1';

    private string $shopUrl;

    private string $shopVersion;

    private string $xmlLog;

    public function setUp(): void
    {
        parent::setUp();

        $this->prepareTestData();
    }

    public function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testRequestFormationWithExistingSerials(): void
    {
        $licenseCaller = new OnlineLicenseCheckCaller(
            oxNew(CurlSpy::class, $this->xmlLog),
            oxNew(OnlineServerEmailBuilder::class),
            oxNew(SimpleXml::class)
        );
        $licenseCheck = new OnlineLicenseCheck($licenseCaller);
        $licenseCheck->setUserCounter(oxNew(UserCounter::class));
        $licenseCheck->setAppServerExporter($this->getApplicationServerExporter());

        $licenseCheck->validateShopSerials();

        $xml = $this->loadRequestLogXml();
        $this->assertEquals(8, $xml->count());
        $this->assertEquals($this->documentName, $xml->getName());
        $this->assertEquals($this->pVersion, $xml->pVersion);
        $this->assertEquals($this->clusterId, $xml->clusterId);
        $this->assertEquals($this->edition, $xml->edition);
        $this->assertEquals($this->shopVersion, $xml->version);
        $this->assertEquals($this->shopUrl, $xml->shopUrl);
        $this->assertEquals($this->productId, $xml->productId);
        /** keys */
        $this->assertEquals(1, $xml->keys->children()->count());
        $this->assertEquals($this->licenseKeyExisting, $xml->keys->key);
        /** productSpecificInformation */
        $this->assertEquals(2, $xml->productSpecificInformation->children()->count());
        /** servers */
        $this->assertEquals(1, $xml->productSpecificInformation->servers->children()->count());
        $this->assertEquals($this->serverId, $xml->productSpecificInformation->servers->server->id);
        $this->assertEquals($this->serverIp, $xml->productSpecificInformation->servers->server->ip);
        $this->assertEquals(
            (string) $this->timestamp,
            $xml->productSpecificInformation->servers->server->lastFrontendUsage
        );
        $this->assertEquals(
            (string) $this->timestamp,
            $xml->productSpecificInformation->servers->server->lastAdminUsage
        );
        /** counters */
        $this->assertEquals(3, $xml->productSpecificInformation->counters->children()->count());
        /** admin users */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[0]->children()->count());
        $this->assertEquals('admin users', (string) $xml->productSpecificInformation->counters->counter[0]->name);
        $this->assertEquals($this->adminUserCount, (int) $xml->productSpecificInformation->counters->counter[0]->value);
        /** active admin users */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[1]->children()->count());
        $this->assertEquals(
            'active admin users',
            (string) $xml->productSpecificInformation->counters->counter[1]->name
        );
        $this->assertEquals($this->adminUserCount, (int) $xml->productSpecificInformation->counters->counter[1]->value);
        /** subShops */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[2]->children()->count());
        $this->assertEquals('subShops', (string) $xml->productSpecificInformation->counters->counter[2]->name);
        $this->assertEquals(1, (int) $xml->productSpecificInformation->counters->counter[2]->value);
    }

    public function testRequestFormationWithNewSerial(): void
    {
        $licenseCaller = new OnlineLicenseCheckCaller(
            oxNew(CurlSpy::class, $this->xmlLog),
            oxNew(OnlineServerEmailBuilder::class),
            oxNew(SimpleXml::class)
        );
        $licenseCheck = new OnlineLicenseCheck($licenseCaller);
        $licenseCheck->setUserCounter(oxNew(UserCounter::class));
        $licenseCheck->setAppServerExporter($this->getApplicationServerExporter());

        $licenseCheck->validateNewSerial($this->licenseKeyNew);

        $xml = $this->loadRequestLogXml();
        $this->assertEquals(8, $xml->count());
        $this->assertEquals($this->documentName, $xml->getName());
        $this->assertEquals($this->pVersion, $xml->pVersion);
        $this->assertEquals($this->clusterId, $xml->clusterId);
        $this->assertEquals($this->edition, $xml->edition);
        $this->assertEquals($this->shopVersion, $xml->version);
        $this->assertEquals($this->shopUrl, $xml->shopUrl);
        $this->assertEquals($this->productId, $xml->productId);
        /** keys */
        $this->assertEquals(2, $xml->keys->children()->count());
        $this->assertEquals($this->licenseKeyExisting, $xml->keys->key[0]);
        $this->assertEquals($this->licenseKeyNew, $xml->keys->key[1]);
        $this->assertEquals('new', $xml->keys->key[1]->attributes()->state);
        /** productSpecificInformation */
        $this->assertEquals(2, $xml->productSpecificInformation->children()->count());
        /** servers */
        $this->assertEquals(1, $xml->productSpecificInformation->servers->children()->count());
        $this->assertEquals($this->serverId, $xml->productSpecificInformation->servers->server->id);
        $this->assertEquals($this->serverIp, $xml->productSpecificInformation->servers->server->ip);
        $this->assertEquals(
            (string) $this->timestamp,
            $xml->productSpecificInformation->servers->server->lastFrontendUsage
        );
        $this->assertEquals(
            (string) $this->timestamp,
            $xml->productSpecificInformation->servers->server->lastAdminUsage
        );
        /** counters */
        $this->assertEquals(3, $xml->productSpecificInformation->counters->children()->count());
        /** admin users */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[0]->children()->count());
        $this->assertEquals('admin users', (string) $xml->productSpecificInformation->counters->counter[0]->name);
        $this->assertEquals($this->adminUserCount, (int) $xml->productSpecificInformation->counters->counter[0]->value);
        /** active admin users */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[1]->children()->count());
        $this->assertEquals(
            'active admin users',
            (string) $xml->productSpecificInformation->counters->counter[1]->name
        );
        $this->assertEquals($this->adminUserCount, (int) $xml->productSpecificInformation->counters->counter[1]->value);
        /** subShops */
        $this->assertEquals(2, $xml->productSpecificInformation->counters->counter[2]->children()->count());
        $this->assertEquals('subShops', (string) $xml->productSpecificInformation->counters->counter[2]->name);
        $this->assertEquals(1, (int) $xml->productSpecificInformation->counters->counter[2]->value);
    }

    private function prepareTestData(): void
    {
        $shopPath = __DIR__ . DIRECTORY_SEPARATOR;

        $this->xmlLog = sprintf('%s/%s.xml', __DIR__, uniqid('request_log_', true));
        $this->licenseKeyExisting = uniqid('license-', true);
        $this->licenseKeyNew = uniqid('license-', true);
        $this->clusterId = uniqid('cluster-', true);
        $this->edition = (new Facts())->getEdition();
        $this->shopVersion = ShopVersion::getVersion();
        $this->shopUrl = Registry::getConfig()->getShopUrl();
        $this->timestamp = Registry::getUtilsDate()->getTime();
        $this->adminUserCount = $this->getActiveAdminCount();

        Registry::getConfig()->setConfigParam('aSerials', [$this->licenseKeyExisting]);
        Registry::getConfig()->setConfigParam('sClusterId', [$this->clusterId]);

        $this->setSeversDataConfiguration();
    }

    private function setSeversDataConfiguration(): void
    {
        Registry::getConfig()
            ->saveSystemConfigParameter(
                'arr',
                "aServersData_{$this->serverId}",
                [
                    'id' => $this->serverId,
                    'timestamp' => $this->timestamp,
                    'ip' => $this->serverIp,
                    'lastFrontendUsage' => $this->timestamp,
                    'lastAdminUsage' => $this->timestamp,
                ]
            );
    }

    private function loadRequestLogXml(): SimpleXMLElement
    {
        return simplexml_load_string(file_get_contents($this->xmlLog));
    }

    private function getApplicationServerExporter(): ApplicationServerExporterInterface
    {
        $appServerDao = oxNew(ApplicationServerDao::class, DatabaseProvider::getDb(), Registry::getConfig());
        $service = oxNew(
            ApplicationServerService::class,
            $appServerDao,
            oxNew(UtilsServer::class),
            Registry::get(UtilsDate::class)->getTime()
        );

        return oxNew(ApplicationServerExporter::class, $service);
    }

    private function cleanUpTestData(): void
    {
        $fileSystem = $this->get('oxid_esales.symfony.file_system');
        if ($fileSystem->exists($this->xmlLog)) {
            $fileSystem->remove($this->xmlLog);
        }
    }

    private function getActiveAdminCount(): int
    {
        return (int) DatabaseProvider::getDb()
            ->getOne("SELECT COUNT(1) FROM oxuser WHERE oxrights != 'user' AND oxactive = 1 ");
    }
}
