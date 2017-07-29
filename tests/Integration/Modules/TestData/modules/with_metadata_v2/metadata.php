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
    'id'           => 'with_metadata_v2',
    'title'        => 'Test extending 1 shop class',
    'description'  => 'Module testing extending 1 shop class',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => ['oxarticle' => 'with_metadata_v2/myarticle'],
    'templates' => array(
        'order_special.tpl'      => 'with_metadata_v2/views/admin/tpl/order_special.tpl',
        'user_connections.tpl'   => 'with_metadata_v2/views/tpl/user_connections.tpl',
    ),
    'controllers'  => [
        'with_metadata_v2_MyModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyModuleController',
        'with_metadata_v2_MyOtherModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Modules\testData\modules\with_metadata_v2\MyOtherModuleController'
    ]
);
