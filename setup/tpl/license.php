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
require "_header.php"; ?>
<textarea readonly="readonly" cols="180" rows="20" class="edittext" style="width: 98%; padding: 7px;"><?php echo $this->getViewParam( "aLicenseText" ); ?></textarea>
<form action="index.php" method="post">
  <input type="hidden" name="istep" value="<?php $this->getSetupStep( 'STEP_DB_INFO' ); ?>">
  <input type="radio" name="iEula" value="1"><?php $this->getText('BUTTON_RADIO_LICENCE_ACCEPT'); ?><br>
  <input type="radio" name="iEula" value="0" checked><?php $this->getText('BUTTON_RADIO_LICENCE_NOT_ACCEPT'); ?><br><br>
  <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
  <input type="submit" id="step2Submit" class="edittext" value="<?php $this->getText('BUTTON_LICENCE'); ?>">
</form>
<?php require "_footer.php";