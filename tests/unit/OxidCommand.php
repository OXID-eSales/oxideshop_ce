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

require_once 'PHPUnit/TextUI/Command.php';

class OxidCommand extends PHPUnit_TextUI_Command
{
     public function __construct()
     {
          $this->longOptions['dbreset='] = 'dbResetHandler';
     }

    /**
     * @param boolean $exit
     */
    public static function main($exit = true)
    {
        $command = new OxidCommand();
        $command->run($_SERVER['argv'], $exit);
    }

    /**
     * @param array   $argv
     * @param boolean $exit
     */
    public function run(array $argv, $exit = true)
    {
        parent::run($argv, false);
    }

    protected function dbResetHandler($value)
    {
       /* require_once 'unit/oxPrinter.php';
        require_once 'unit/dbRestore.php';
        $dbM = new DbRestore();
        $dbM->dumpDB();*/
    }

}
