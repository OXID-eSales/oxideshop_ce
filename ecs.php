<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBetweenImportGroupsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRules([
        BlankLineBetweenImportGroupsFixer::class,
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
        strict: true,
    )
    ->withConfiguredRule(
        HeaderCommentFixer::class,
        [
            'header' => 'Copyright © OXID eSales AG. All rights reserved.
See LICENSE file for license details.',
            'location' => 'after_open',
            'comment_type' => 'PHPDoc'
        ]
    )
    ->withConfiguredRule(
        ClassAttributesSeparationFixer::class,
        [
            'elements' => [
                'property' => ClassAttributesSeparationFixer::SPACING_NONE,
                'const' => ClassAttributesSeparationFixer::SPACING_NONE,
            ],
        ]
    )
    ->withConfiguredRule(
        OrderedImportsFixer::class,
        [
            'sort_algorithm' => OrderedImportsFixer::SORT_ALPHA,
            'imports_order' => [
                OrderedImportsFixer::IMPORT_TYPE_CLASS,
                OrderedImportsFixer::IMPORT_TYPE_FUNCTION,
                OrderedImportsFixer::IMPORT_TYPE_CONST,
            ]
        ]
    )
    ->withSkip(
        [
            NotOperatorWithSuccessorSpaceFixer::class,
            DeclareStrictTypesFixer::class => [
                '*Interface.php',
            ]
        ],
    );
