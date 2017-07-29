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
use OxidEsales\Eshop\Application\Controller\ContentController;

/**
 * Module information
 */
$aModule = array(
    'id'           => 'namespace1',
    'title'        => 'Namespaced module #1',
    'description'  => 'Appends "+ namespace1 to content title"',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID',
    'extend'      => [
        ContentController::class => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\namespace1\Controllers\ContentController::class
    ],
);
