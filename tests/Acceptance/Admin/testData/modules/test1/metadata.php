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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

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
        array('group' => 'settingsEmpty', 'name' => 'testEmptySelectConfig',   'type' => 'select',   'value' => '', 'constrains' => '0|1|2'),
        array('group' => 'settingsEmpty', 'name' => 'testEmptyPasswordConfig', 'type' => 'password', 'value' => ''),

        array('group' => 'settingsFilled', 'name' => 'testFilledBoolConfig',     'type' => 'bool',     'value' => 'true'),
        array('group' => 'settingsFilled', 'name' => 'testFilledStrConfig',      'type' => 'str',      'value' => 'testStr'),
        array('group' => 'settingsFilled', 'name' => 'testFilledArrConfig',      'type' => 'arr',      'value' => array('option1', 'option2')),
        array('group' => 'settingsFilled', 'name' => 'testFilledAArrConfig',     'type' => 'aarr',     'value' => array('key1' => 'option1', 'key2' => 'option2')),
        array('group' => 'settingsFilled', 'name' => 'testFilledSelectConfig',   'type' => 'select',   'value' => '2', 'constrains' => '0|1|2', 'position' => 3),
        array('group' => 'settingsFilled', 'name' => 'testFilledPasswordConfig', 'type' => 'password', 'value' => 'testPassword'),
    )
);
