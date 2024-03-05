<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ScriptLogic;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function sprintf;

final class ScriptLogicTest extends IntegrationTestCase
{
    private Config $config;
    private ScriptLogic $scriptLogic;

    public function setup(): void
    {
        parent::setUp();
        $this->config = Registry::getConfig();
        $this->config->setConfigParam('iDebug', -1);

        $this->scriptLogic = new ScriptLogic();
    }

    public function testIncludeFileNotExists(): void
    {
        $this->config->setConfigParam('iDebug', 0);

        $this->scriptLogic->include('somescript.js');

        $this->assertNull($this->config->getGlobalParameter('includes'));
    }

    public function testIncludeFileExists(): void
    {
        $this->scriptLogic->include('http://someurl/src/js/libs/jquery.min.js', 3);

        $this->assertArrayHasKey(3, $this->config->getGlobalParameter('includes'));
        $this->assertContains(
            'http://someurl/src/js/libs/jquery.min.js',
            $this->config->getGlobalParameter('includes')[3]
        );
    }

    public function testAddNotDynamic(): void
    {
        $this->scriptLogic->add('oxidadd');

        $this->assertContains('oxidadd', $this->config->getGlobalParameter('scripts'));
    }

    public function testAddDynamic(): void
    {
        $this->scriptLogic->add('oxidadddynamic', true);

        $this->assertContains('oxidadddynamic', $this->config->getGlobalParameter('scripts_dynamic'));
    }

    #[DataProvider('addWidgetProvider')]
    public function testRenderAddWidget(string $script, string $output): void
    {
        $expected = sprintf(
            <<<EOF
<script type='text/javascript'>
    window.addEventListener('load', function() {
        WidgetsHandler.registerFunction('%s', 'somewidget');
        }, false )
</script>
EOF,
            $output
        );
        $this->scriptLogic->add($script);

        $this->assertEquals($expected, $this->scriptLogic->render('somewidget', true));
    }

    public static function addWidgetProvider(): array
    {
        return [
            ['oxidadd', 'oxidadd'],
            ['"oxidadd"', '"oxidadd"'],
            ["'oxidadd'", "\\'oxidadd\\'"],
            ["oxid\r\nadd", 'oxid\nadd'],
            ["oxid\nadd", 'oxid\nadd'],
        ];
    }

    public function testRenderIncludeWidget(): void
    {
        $this->scriptLogic->include('http://someurl/src/js/libs/jquery.min.js');

        $expected = <<<HTML
<script type='text/javascript'>
    window.addEventListener('load', function() {
        WidgetsHandler.registerFile('http://someurl/src/js/libs/jquery.min.js', 'somewidget');
    }, false)
</script>
HTML;

        $this->assertEquals($expected, $this->scriptLogic->render('somewidget', true));
    }
}
