<?php

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => '[{$oScaffold->sModuleId}]',
    'title'       => array(
        'de' => '[TR - [{$oScaffold->sModuleTitle}]]',
        'en' => '[{$oScaffold->sModuleTitle}]',
    ),
    'description' => array(
        'de' => '',
        'en' => '',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '0.0.1-DEV',
    'author'      => '[{$oScaffold->sAuthor}]',
    'url'         => '[{$oScaffold->sUrl}]',
    'email'       => '[{$oScaffold->sEmail}]',
    'extend'      => array(
        // '<oxclass>'   => '<vendor/module/path_to_file>',
    ),
    'files'       => array(
        '[{$oScaffold->sVendor}][{$oScaffold->sModuleName|lower}]module' => '[{if $oScaffold->sVendor}][{$oScaffold->sVendor}]/[{/if}][{$oScaffold->sModuleName|lower}]/core/[{$oScaffold->sVendor}][{$oScaffold->sModuleName|lower}]module.php',
    ),
    'templates'   => array(
        // '<template.tpl>' => '<vendor/module/path_to_template.tpl>',
    ),
    'blocks'      => array(
        // array(
        //     'template' => '<path_to_core_template.tpl>',
        //     'block'    => '<block_name>',
        //     'file'     => '<views/block/my_block.tpl>'
        // ),
    ),
    'settings'    => array(),
    'events'      => array(
        'onActivate'   => '[{$oScaffold->sVendor}][{$oScaffold->sModuleName}]Module::onActivate',
        'onDeactivate' => '[{$oScaffold->sVendor}][{$oScaffold->sModuleName}]Module::onDeactivate',
    ),
);
