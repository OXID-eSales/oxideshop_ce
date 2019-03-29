<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test1',
    'title'        => 'Test module #1',
    'description'  => 'Appends "+ test1 to content title"',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID',
    'extend'      => array(
        'content' => 'test1/controllers/test1content'
    ),
    'settings' => array(
        array('group' => 'settingsEmpty', 'name' => 'testEmptyBoolConfig',     'type' => 'bool',     'value' => 'false'),
        array('group' => 'settingsEmpty', 'name' => 'testEmptyStrConfig',      'type' => 'str',      'value' => ''),
        array('group' => 'settingsEmpty', 'name' => 'testEmptyArrConfig',      'type' => 'arr',      'value' => ''),
        array('group' => 'settingsEmpty', 'name' => 'testEmptyAArrConfig',     'type' => 'aarr',     'value' => ''),
        array('group' => 'settingsEmpty', 'name' => 'testEmptySelectConfig',   'type' => 'select',   'value' => '', 'constraints' => '0|1|2'),
        array('group' => 'settingsEmpty', 'name' => 'testEmptyPasswordConfig', 'type' => 'password', 'value' => ''),

        array('group' => 'settingsFilled', 'name' => 'testFilledBoolConfig',     'type' => 'bool',     'value' => 'true'),
        array('group' => 'settingsFilled', 'name' => 'testFilledStrConfig',      'type' => 'str',      'value' => 'testStr'),
        array('group' => 'settingsFilled', 'name' => 'testFilledArrConfig',      'type' => 'arr',      'value' => array('option1', 'option2')),
        array('group' => 'settingsFilled', 'name' => 'testFilledAArrConfig',     'type' => 'aarr',     'value' => array('key1' => 'option1', 'key2' => 'option2')),
        array('group' => 'settingsFilled', 'name' => 'testFilledSelectConfig',   'type' => 'select',   'value' => '2', 'constraints' => '0|1|2', 'position' => 3),
        array('group' => 'settingsFilled', 'name' => 'testFilledPasswordConfig', 'type' => 'password', 'value' => 'testPassword'),
    )
);
