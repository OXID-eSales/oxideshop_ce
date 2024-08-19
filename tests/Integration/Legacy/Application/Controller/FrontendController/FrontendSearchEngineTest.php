<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\FrontendController;

use Exception;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class FrontendSearchEngineTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->truncateDatabase();
    }

    public function tearDown(): void
    {
        $this->unsetRequest();

        parent::tearDown();
    }

    public static function providerSeoLoggingScenarios(): array
    {
        return [
            [0, false, false, "Url should not be processed"],
            [1, false, false, "Url should not be processed"],
            [0, true, true, "Url should be processed"],
            [1, true, true, "Url should be processed"],
        ];
    }

    #[DataProvider('providerSeoLoggingScenarios')]
    public function testRequestProcessingScenarios($productive, bool $seoLogging, bool $expected, string $message): void
    {
        $this->setRequest();
        $this->setUtilsSpy();
        Registry::getConfig()->setConfigParam('blProductive', $productive);

        $context = ContainerFacade::get(ContextInterface::class);
        $this->setParameter('oxid_log_not_seo_urls', $seoLogging);
        $this->setParameter('oxid_build_directory', $context->getCacheDirectory());
        $this->attachContainerToContainerFactory();

        $frontend = $this->getFrontendSeoLoggingSpy();

        try {
            $frontend->processRequest();
        } catch (Exception $oEx) {
            $this->fail('Error executing "testRequestProcessingScenarios" test: ' . $oEx->getMessage());
        }

        $ident = $this->getIdent($context->getDefaultShopId());

        $this->assertEquals(
            $expected,
            (bool)DatabaseProvider::getDb()->getOne("select 1 from oxseologs where oxident='$ident'"),
            $message
        );
    }

    public function testNoIndexRequestCanNotRedirect(): void
    {
        $this->setRequest();
        $this->setUtilsSpy();

        $frontend = $this->getFrontendNoIndexSpy();

        try {
            $frontend->processRequest();
        } catch (Exception $oEx) {
            $this->fail('Error executing "testNoIndexRequestCanNotRedirect" test: ' . $oEx->getMessage());
        }

        $ident = $this->getIdent(ContainerFacade::get(ContextInterface::class)->getDefaultShopId());
        $this->assertfalse((bool)DatabaseProvider::getDb()->getOne("select 1 from oxseologs where oxident='$ident'"));
    }

    private function setRequest(): void
    {
        $_SERVER["REQUEST_METHOD"] = 'GET';
        $_SERVER['REQUEST_URI'] = 'index.php?param1=value1&param2=value2';
    }

    private function getIdent(int $shopId): string
    {
        $uri = strtolower(str_replace('&', '&amp;', $_SERVER['REQUEST_URI']));

        return md5($uri . $shopId . Registry::getLang()->getBaseLanguage());
    }

    private function truncateDatabase(): void
    {
        DatabaseProvider::getDb()->execute('DELETE FROM `oxseologs`');
    }

    private function unsetRequest(): void
    {
        unset($_SERVER["REQUEST_METHOD"]);
        unset($_SERVER['REQUEST_URI']);
    }

    private function setUtilsSpy(): void
    {
        $utils = new class extends Utils {
            public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 302)
            {
                $aArgs = func_get_args();
                throw new Exception($aArgs[0]);
            }
        };

        Registry::set(Utils::class, $utils);
    }

    private function getFrontendSeoLoggingSpy(): FrontendController
    {
        return new class extends FrontendController {
            public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 302)
            {
                $aArgs = func_get_args();
                throw new Exception($aArgs[0]);
            }

            public function canRedirect(): bool
            {
                return false;
            }

            public function isAdmin(): bool
            {
                return false;
            }
        };
    }

    private function getFrontendNoIndexSpy(): FrontendController
    {
        return new class extends FrontendController {
            public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 302)
            {
                $aArgs = func_get_args();
                throw new Exception($aArgs[0]);
            }

            public function forceNoIndex()
            {
                throw new Exception('forceIndex method should not be called');
            }

            public function noIndex(): int
            {
                return VIEW_INDEXSTATE_NOINDEXFOLLOW;
            }

            public function canRedirect(): bool
            {
                return false;
            }

            public function isAdmin(): bool
            {
                return false;
            }
        };
    }
}
