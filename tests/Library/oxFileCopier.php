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
 *
 * @link http://www.oxid-esales.com
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 */

/**
 * Class for copying files. Can copy files locally or to external server.
 */
class oxFileCopier
{

    /**
     * Copy files to shop
     *
     * @param string $sSource          File or directory to copy.
     * @param string $sTarget          Path where to copy.
     * @param bool   $blSetPermissions Whether to set given Target permissions to 777.
     */
    public function copyFiles($sSource, $sTarget, $blSetPermissions = false)
    {
        if (strpos($sTarget, ':') !== false && strpos($sTarget, '@') !== false) {
            $this->_executeCommand("scp -rp ".escapeshellarg($sSource."/.")." ".escapeshellarg($sTarget));
            if ($blSetPermissions) {
                list($sServer, $sDirectory) = explode(":", $sTarget, 2);
                $this->_executeCommand("ssh ".escapeshellarg($sServer)." chmod 777 ".escapeshellarg('/'.$sDirectory));
            }
        } else {
            $this->_executeCommand("cp -frT ".escapeshellarg($sSource)." ".escapeshellarg($sTarget));
            if ($blSetPermissions) {
                $this->_executeCommand("chmod 777 " . escapeshellarg($sTarget));
            }
        }
    }

    /**
     * Executes shell command.
     *
     * @param string $sCommand
     *
     * @throws Exception
     *
     * @return string Output of command.
     */
    private function _executeCommand($sCommand)
    {
        $blResult = @exec($sCommand, $sOutput, $iCode);
        $sOutput = implode("\n", $sOutput);

        if ($blResult === false) {
            throw new Exception("Failed to execute command '$sCommand' with message: [$iCode] '$sOutput'");
        }

        return $sOutput;
    }
}
