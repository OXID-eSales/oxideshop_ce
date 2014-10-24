<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Database update command
 *
 * Updates OXID database views
 */
class DatabaseUpdateCommand extends oxConsoleCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('db:update');
        $this->setDescription('Updates database views');
    }

    /**
     * {@inheritdoc}
     */
    public function help(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Usage: db:update');
        $oOutput->writeLn();
        $oOutput->writeLn('Updates OXID shop database views');
        $oOutput->writeLn();
        $oOutput->writeLn('If there are some changes in database schema it is always a good');
        $oOutput->writeLn('idea to run database update command');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(oxIOutput $oOutput)
    {
        $oOutput->writeLn('Updating database views');

        /** @var oxDbMetaDataHandler $oDbHandler */
        $oDbHandler = oxNew('oxDbMetaDataHandler');

        if (!$oDbHandler->updateViews()) {
            $oOutput->writeLn('[ERROR] Could not update database views');
            return;
        }

        $oOutput->writeLn('Database views updated successfully');
    }
}