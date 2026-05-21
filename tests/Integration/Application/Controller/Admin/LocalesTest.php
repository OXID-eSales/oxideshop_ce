<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\Locales;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Exception\LocaleNotFoundException;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\LocaleServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LocalesTest extends IntegrationTestCase
{
    public function testRenderReturnsAllLocalesWithActiveFlags(): void
    {
        $controller = $this->get(Locales::class);
        $controller->render();

        $locales = $controller->getViewData()['locales'];

        $this->assertNotEmpty($locales);

        $deDe = $this->findLocaleByCode($locales, 'de_DE');
        $this->assertSame('Deutsch (Deutschland)', $deDe['name']);
        $this->assertTrue($deDe['active']);

        $enGb = $this->findLocaleByCode($locales, 'en_GB');
        $this->assertSame('English (United Kingdom)', $enGb['name']);
        $this->assertTrue($enGb['active']);
    }

    public function testSaveUpdatesExistingLocale(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'German', 'fallback' => 'en_GB', 'active' => '1'],
            'en_GB' => ['name' => 'English', 'fallback' => 'de_DE', 'active' => '1'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $service = $this->get(LocaleServiceInterface::class);
        $this->assertSame('German', $service->getByCode('de_DE')->getName());
        $this->assertSame('en_GB', $service->getByCode('de_DE')->getFallbackCode());
        $this->assertSame('English', $service->getByCode('en_GB')->getName());
    }

    public function testSaveAddsNewLocale(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE', 'active' => '1'],
        ]);
        $request->request->set('newLocales', [
            ['code' => 'fr_FR', 'name' => 'Français', 'fallback' => 'de_DE', 'active' => '1'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $service = $this->get(LocaleServiceInterface::class);
        $locale = $service->getByCode('fr_FR');
        $this->assertSame('Français', $locale->getName());
        $this->assertSame('de_DE', $locale->getFallbackCode());
    }

    public function testSaveSkipsNewLocaleWithEmptyCode(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE', 'active' => '1'],
        ]);
        $request->request->set('newLocales', [
            ['code' => '', 'name' => 'Empty', 'fallback' => 'de_DE'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $service = $this->get(LocaleServiceInterface::class);
        $this->assertCount(2, $service->getAll());
    }

    public function testSaveSkipsDuplicateNewLocaleAndAddsValidOnes(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE', 'active' => '1'],
        ]);
        $request->request->set('newLocales', [
            ['code' => 'de_DE', 'name' => 'Duplicate', 'fallback' => 'de_DE', 'active' => '1'],
            ['code' => 'fr_FR', 'name' => 'Français', 'fallback' => 'de_DE', 'active' => '1'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $service = $this->get(LocaleServiceInterface::class);
        $this->assertSame('Français', $service->getByCode('fr_FR')->getName());
        $this->assertSame('Deutsch (Deutschland)', $service->getByCode('de_DE')->getName());
        $this->assertCount(3, $service->getAll());
    }

    public function testSaveDeactivatesLocale(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $service = $this->get(LocaleServiceInterface::class);
        $shopLocales = array_map(
            fn($l) => $l->getCode(),
            $service->getForShop(1)
        );
        $this->assertContains('de_DE', $shopLocales);
        $this->assertNotContains('en_GB', $shopLocales);
    }

    public function testSaveActivatesLocale(): void
    {
        $service = $this->get(LocaleServiceInterface::class);
        $service->removeFromShop('en_GB', 1);

        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE', 'active' => '1'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $shopLocales = array_map(
            fn($l) => $l->getCode(),
            $service->getForShop(1)
        );
        $this->assertContains('en_GB', $shopLocales);
    }

    public function testDeleteRemovesLocale(): void
    {
        $service = $this->get(LocaleServiceInterface::class);
        $service->add(new Locale('fr_FR', 'Français', 'de_DE'));

        $request = $this->get(Request::class);
        $request->request->set('localeCode', 'fr_FR');

        $controller = $this->get(Locales::class);
        $controller->delete();

        $this->expectException(LocaleNotFoundException::class);
        $service->getByCode('fr_FR');
    }

    public function testRenderShowsNewLocaleAfterAdd(): void
    {
        $request = $this->get(Request::class);
        $request->request->set('locales', [
            'de_DE' => ['name' => 'Deutsch (Deutschland)', 'fallback' => '', 'active' => '1'],
            'en_GB' => ['name' => 'English (United Kingdom)', 'fallback' => 'de_DE', 'active' => '1'],
        ]);
        $request->request->set('newLocales', [
            ['code' => 'fr_FR', 'name' => 'Français', 'fallback' => 'de_DE', 'active' => '1'],
        ]);

        $controller = $this->get(Locales::class);
        $controller->save();

        $request->request->remove('locales');
        $request->request->remove('newLocales');

        $controller2 = $this->get(Locales::class);
        $controller2->render();

        $locales = $controller2->getViewData()['locales'];
        $frFr = $this->findLocaleByCode($locales, 'fr_FR');
        $this->assertSame('Français', $frFr['name']);
        $this->assertTrue($frFr['active']);
    }

    private function findLocaleByCode(array $locales, string $code): array
    {
        foreach ($locales as $locale) {
            if ($locale['code'] === $code) {
                return $locale;
            }
        }
        $this->fail("Locale '$code' not found in view data");
    }
}
