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
require "_header.php"; ?>
<strong><?php $this->getText('STEP_1_DESC'); ?></strong><br>
<br>
<form action="index.php" method="post">
<table cellpadding="1" cellspacing="0">
    <tr>
        <td style="padding-top: 5px;"><?php $this->getText('SELECT_SHOP_LOCATION'); ?>: </td>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" height="29">
              <tr>
                <td style="padding-right: 3px;">
                    <select name="location_lang" style="font-size: 11px;">
                        <option value=""><?php $this->getText('SELECT_PLEASE_CHOOSE'); ?></option>
                        <?php
                        $aLocations   = $this->getViewParam( "aLocations" );
                        $sLanguage   = $this->getViewParam( "sLanguage" );
                        $sLocationLang = $this->getViewParam( "sLocationLang" );

                        if ( isset( $aLocations[$sLanguage] ) ) {
                            foreach ( $aLocations[$sLanguage] as $sKey => $sValue ) {
                                $sSelected = ( $sLocationLang !== null && $sLocationLang == $sKey ) ? 'selected' : '';
                                ?><option value="<?php echo $sKey; ?>" <?php echo $sSelected; ?>><?php echo $sValue; ?></option><?php
                            }
                        }
                        ?>
                    </select>
                </td>
                <td style="padding: 0px 5px;">
                    <a href="#" style="display:block;width:18px;height:18px;background: #ddd;border: 1px solid #ccc;border-radius: 5px;line-height: 18px;text-align: center;font-weight: bold;color: #777;" onmouseover="document.getElementById('marketHelpBox').style.display = '';" onmouseout="document.getElementById('marketHelpBox').style.display = 'none';">?</a>
                    <div id="marketHelpBox" style="position: absolute;margin-top:5px;border: 1px solid #c1c1c1; background: #ddd;padding: 10px;border-radius: 5px;display: none;width:300px;">
                        <?php $this->getText('SELECT_SHOP_LOCATION_HINT'); ?>
                    </div>
                </td>
                <?php if ($editionSelector->isCommunity()) { ?>
                    <noscript>
                    <td>
                        <input type="submit" name="setup_lang_submit" value="<?php $this->getText('SELECT_SETUP_LANG_SUBMIT'); ?>" style="font-size: 11px;">
                    </td>
                    </noscript>
                    <td>
                    &nbsp;&nbsp;
                        <input type="hidden" value="false" name="send_technical_information_to_oxid">
                        <input type="checkbox" value="true" id="send_technical_information_to_oxid_checkbox" name="send_technical_information_to_oxid" valign="" style="vertical-align:middle; width:20px; height:22px;" >
                        <?php $this->getText('SHOP_CONFIG_SEND_TECHNICAL_INFORMATION_TO_OXID'); ?>
                        &nbsp;
                    </td>
                    <td>
                        <a href="#" style="display:block;width:18px;height:18px;background: #ddd;border: 1px solid #ccc;border-radius: 5px;line-height: 18px;text-align: center;font-weight: bold;color: #777;" onmouseover="document.getElementById('send_technical_information_to_oxid_description').style.display = '';" onmouseout="document.getElementById('send_technical_information_to_oxid_description').style.display = 'none';">?</a>
                        <div id="send_technical_information_to_oxid_description" style="position: absolute;margin-top:5px;border: 1px solid #c1c1c1; background: #ddd;padding: 10px;border-radius: 5px;display: none;width:300px;">
                            <?php $this->getText('HELP_SHOP_CONFIG_SEND_TECHNICAL_INFORMATION_TO_OXID'); ?>
                        </div>
                    </td>
                <?php } ?>
            </tr>
          </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 5px;"><?php $this->getText('SELECT_DELIVERY_COUNTRY'); ?>: </td>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" height="29">
                <tr>
                    <td>
                        <select name="country_lang" style="font-size: 11px;">
                            <?php
                                $aCountries   = $this->getViewParam( "aCountries" );
                                $sLanguage   = $this->getViewParam( "sLanguage" );
                                $sCountryLang = $this->getViewParam( "sCountryLang" );

                                if ( isset( $aCountries[$sLanguage] ) ) {
                                    foreach ( $aCountries[$sLanguage] as $sKey => $sValue ) {
                                        $sSelected = ( $sCountryLang !== null && $sCountryLang == $sKey ) ? 'selected' : '';
                                        ?><option value="<?php echo $sKey; ?>" <?php echo $sSelected; ?>><?php echo $sValue; ?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </td>
                    <td style="padding: 0px 5px;">
                        <a href="#" style="display:block;width:18px;height:18px;background: #ddd;border: 1px solid #ccc;border-radius: 5px;line-height: 18px;text-align: center;font-weight: bold;color: #777;" onmouseover="document.getElementById('countryHelpBox').style.display = '';" onmouseout="document.getElementById('countryHelpBox').style.display = 'none';">?</a>
                        <div id="countryHelpBox" style="position: absolute;margin-top:5px;border: 1px solid #c1c1c1; background: #ddd;padding: 10px;border-radius: 5px;display: none;width:300px;">
                            <?php $this->getText('SELECT_DELIVERY_COUNTRY_HINT'); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding-top: 5px;"><?php $this->getText('SELECT_SHOP_LANG'); ?>: </td>
        <td>
            <table cellpadding="0" cellspacing="0" border="0" height="29">
                <tr>
                    <td>
                        <select name="sShopLang" style="font-size: 11px;">
                            <?php
                            $aLanguages = $this->getViewParam( "aLanguages" );
                            foreach ( $aLanguages as $sLangId => $sLangTitle ) {
                                ?>
                                <option value="<?php echo $sLangId; ?>" <?php if ( $this->getViewParam( "sShopLang" ) == $sLangId ) echo 'selected'; ?>><?php echo $sLangTitle; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td style="padding: 0px 5px;">
                        <a href="#" style="display:block;width:18px;height:18px;background: #ddd;border: 1px solid #ccc;border-radius: 5px;line-height: 18px;text-align: center;font-weight: bold;color: #777;" onmouseover="document.getElementById('langHelpBox').style.display = '';" onmouseout="document.getElementById('langHelpBox').style.display = 'none';">?</a>
                        <div id="langHelpBox" style="position: absolute;margin-top:5px;border: 1px solid #c1c1c1; background: #ddd;padding: 10px;border-radius: 5px;display: none;width:300px;">
                            <?php $this->getText('SELECT_SHOP_LANG_HINT'); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
   </table>
    <br>
    <input type="hidden" value="false" name="check_for_updates">
    <input type="checkbox" id="check_for_updates_ckbox" value="true" name="check_for_updates" valign="" style="vertical-align:middle; width:20px; height:22px;" >
    <?php $this->getText('STEP_1_CHECK_UPDATES'); ?>

    <br><br>
    <?php $this->getText('STEP_1_TEXT'); ?>
    <br><br>
    <?php $this->getText('STEP_1_ADDRESS'); ?>
    <br>
    <input type="hidden" name="istep" value="<?php $this->getSetupStep( 'STEP_LICENSE' ); ?>">
    <input type="hidden" name="sid" value="<?php $this->getSid(); ?>">
    <input type="submit" id="step1Submit" class="edittext" value="<?php $this->getText('BUTTON_BEGIN_INSTALL'); ?>">
</form>
<?php require "_footer.php";