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
/**
 * Metadata version
 */
$sMetadataVersion = '2.0';
/**
 * Module information
 */
$aModule = array(
    'id'           => 'test_module_controller_routing_ns',
    'title'        => 'Test metadata_controllers_feature_ns',
    'description'  => '',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'controllers'  => [
        'test_module_controller_routing_ns_MyModuleController' => OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\test_module_controller_routing_ns\MyModuleController::class,
        'test_module_controller_routing_ns_MyOtherModuleController' => OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\test_module_controller_routing_ns\MyOtherModuleController::class,
    ],
    'templates' => [
        'test_module_controller_routing_ns.tpl' => 'test_module_controller_routing_ns/test_module_controller_routing_ns.tpl',
        'test_module_controller_routing_ns_other.tpl' => 'test_module_controller_routing_ns/test_module_controller_routing_ns_other.tpl'
    ]
);
