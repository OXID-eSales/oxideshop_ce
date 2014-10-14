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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Widget parent.
 * Gather functionality needed for all widgets but not for other views.
 */
class oxWidget extends oxUBase
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * Widget should rewrite and use only those which  it needs.
     * @var array
     */
    protected $_aComponentNames = array();

    /**
     * If active load components
     * Widgets loads active view components
     *
     * @var array
     */
    protected $_blLoadComponents = false;

    /**
     * Sets self::$_aCollectedComponentNames to null, as views and widgets
     * controllers loads different components and calls parent::init()
     *
     * @return null
     */
    public function init()
    {
        self::$_aCollectedComponentNames = null;

        if ( !empty( $this->_aComponentNames ) ) {
            foreach ( $this->_aComponentNames as $sComponentName => $sCompCache ) {
                $oActTopView = $this->getConfig()->getTopActiveView();
                if ( $oActTopView ) {
                    $this->_oaComponents[$sComponentName] = $oActTopView->getComponent( $sComponentName );
                    if ( !isset( $this->_oaComponents[$sComponentName] ) ) {
                        $this->_blLoadComponents = true;
                        break;
                    } else {
                        $this->_oaComponents[$sComponentName]->setParent( $this );
                    }
                }
            }
        }

        parent::init();

    }

    /**
     * In widgets we do not need to parse seo and do any work related to that
     * Shop main control is responsible for that, and that has to be done once
     *
     * @return null|void
     */
    protected function _processRequest()
    {
    }

}