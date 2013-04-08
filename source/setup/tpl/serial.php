<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   setup
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: lang.php 25584 2010-02-03 12:11:40Z arvydas $
 */
require "_header.php"; ?>
<b><?php $this->getText('STEP_5_DESC'); ?></b><br>
<br>
<form action="index.php" method="post">
<input type="hidden" name="istep" value="<?php $this->getSetupStep('STEP_SERIAL_SAVE'); ?>">

<table cellpadding="0" cellspacing="5" border="0">
  <tr>
    <td><?php $this->getText('STEP_5_LICENCE_KEY'); ?>:</td>
    <td>&nbsp;&nbsp;<input size="47" name="sLicence" class="editinput" value="<?php echo $this->getViewParam( "sLicense" ); ?>"></td>
    <td>&nbsp;&nbsp;<input type="submit" id="step5Submit" class="edittext" value="<?php $this->getText('BUTTON_WRITE_LICENCE'); ?>"></td>
  </tr>
</table>
<br>
<input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
</form>
<?php
$this->getText('STEP_5_LICENCE_DESC');
require "_footer.php";