<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set(Option::SETS, [
            SetList::PSR_1,
            SetList::PSR_12,
            SetList::CLEAN_CODE,
            SetList::ARRAY,
            SetList::COMMENTS,
            SetList::DOCBLOCK,
            SetList::NAMESPACES,
            SetList::STRICT,
            SetList::DEAD_CODE,
            SetList::DOCTRINE_ANNOTATIONS,
            SetList::SYMFONY,
            SetList::SYMFONY_RISKY,
            SetList::PHP_56_MIGRATION_RISKY,
            SetList::PHP_70_MIGRATION,
            SetList::PHP_71_MIGRATION,
            SetList::PHP_71_MIGRATION_RISKY,
            SetList::PHP_73_MIGRATION,
        ])
        ->set(Option::PATHS, [
            __FILE__, __DIR__ . '/source',
        ]);

    $containerConfigurator
        ->services()
        ->set(ConcatSpaceFixer::class)->call('configure', [[
            'spacing' => 'one',
        ]])
        ->set(CastSpacesFixer::class)->call('configure', [[
            'space' => 'none',
        ]]);
};
