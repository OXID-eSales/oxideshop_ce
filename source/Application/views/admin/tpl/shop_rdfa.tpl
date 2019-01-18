[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}
//-->
</script>
[{include file="include/rdfa_script.tpl"}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{assign var="aContents" value=$oView->getContentList()}]
[{assign var="customers" value=$oView->getCustomers()}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="cl" value="shop_rdfa">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>
<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="shop_rdfa">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

[{block name="admin_shop_rdfa_tech_options"}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_RDFA_TECH_CONFIG"}]</b></a>
            <dl>
                <dt>
                    <input type=hidden name="confbools[blRDFaEmbedding]" value="false">
                    <input type=checkbox name="confbools[blRDFaEmbedding]" value="true"  [{if ($confbools.blRDFaEmbedding)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_EMBEDDING"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_EMBEDDING"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[sRDFaBusinessEntityLoc]" class="select" [{$readonly}]>
                        [{foreach key=key item=item from=$aContents}]
                               <option value="[{$item->oxcontents__oxloadid->value}]" [{if ($confstrs.sRDFaBusinessEntityLoc == $item->oxcontents__oxloadid->value)}]selected[{/if}]>[{$item->oxcontents__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_CONTENT_OFFERER"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_CONTENT_OFFERER"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <div class="spacer"></div>
                <div>[{oxmultilang ident="SHOP_RDFA_ASSIGN_PAYMENT"}]</div>
                <dt>
                    <select name="confstrs[sRDFaPaymentChargeSpecLoc]" class="select" [{$readonly}]>
                        [{foreach key=key item=item from=$aContents}]
                               <option value="[{$item->oxcontents__oxloadid->value}]" [{if ($confstrs.sRDFaPaymentChargeSpecLoc == $item->oxcontents__oxloadid->value)}]selected[{/if}]>[{$item->oxcontents__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_CONTENT_PAYMENT"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_CONTENT_PAYMENT"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <div class="spacer"></div>
                <div>[{oxmultilang ident="SHOP_RDFA_ASSIGN_DELIVERY"}]</div>
                <dt>
                    <select name="confstrs[sRDFaDeliveryChargeSpecLoc]" class="select" [{$readonly}]>
                        [{foreach key=key item=item from=$aContents}]
                               <option value="[{$item->oxcontents__oxloadid->value}]" [{if ($confstrs.sRDFaDeliveryChargeSpecLoc == $item->oxcontents__oxloadid->value)}]selected[{/if}]>[{$item->oxcontents__oxtitle->value}]</option>
                        [{/foreach}]
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_CONTENT_DELIVERY"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_CONTENT_DELIVERY"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[iRDFaVAT]" class="select" [{$readonly}]>
                        <option value="0" [{if ($confstrs.iRDFaVAT == 0)}]selected[{/if}]>-</option>
                        <option value="1" [{if ($confstrs.iRDFaVAT == 1)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_VAT_INC"}]</option>
                        <option value="2" [{if ($confstrs.iRDFaVAT == 2)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_VAT_EX"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_VAT"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_VAT"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[iRDFaPriceValidity]" [{$readonly}]>
                        <option value="0" [{if ($confstrs.iRDFaPriceValidity == 0)}]selected[{/if}]>-</option>
                        <option value="1" [{if ($confstrs.iRDFaPriceValidity == 1)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_1_DAY"}]</option>
                        <option value="3" [{if ($confstrs.iRDFaPriceValidity == 3)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_3_DAYS"}]</option>
                        <option value="7" [{if ($confstrs.iRDFaPriceValidity == 7)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_7_DAYS"}]</option>
                        <option value="14" [{if ($confstrs.iRDFaPriceValidity == 14)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_14_DAYS"}]</option>
                        <option value="30" [{if ($confstrs.iRDFaPriceValidity == 30)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_30_DAYS"}]</option>
                        <option value="178" [{if ($confstrs.iRDFaPriceValidity == 178)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_178_DAYS"}]</option>
                        <option value="356" [{if ($confstrs.iRDFaPriceValidity == 356)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_356_DAYS"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_DURATION_PRICES"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_DURATION_PRICES"}]
                </dd>
                <div class="spacer"></div>
            </dl>

         </div>
    </div>
[{/block}]

[{block name="admin_shop_rdfa_offerer_options"}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_RDFA_DATA_OFFERER"}]</b></a>
            <dl>
                <div>[{oxmultilang ident="SHOP_RDFA_DATA_MASTER"}]</div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxcompany->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_MAIN_COMPANY"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxstreet->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_STREETNUM"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="5" maxlength="255" value="[{$edit->oxshops__oxzip->value}]" style="color: #777777;" readonly>
                    <input type="text" class="editinput" size="26" maxlength="255" value="[{$edit->oxshops__oxcity->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_ZIPCITY"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxcountry->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_COUNTRY"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxtelefon->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_TELEPHONE"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxtelefax->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_FAX"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type="text" class="editinput" size="35" maxlength="128" value="[{$edit->oxshops__oxurl->value}]" style="color: #777777;" readonly>
                </dt>
                <dd>
                    [{oxmultilang ident="GENERAL_URL"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <div>[{oxmultilang ident="SHOP_RDFA_DATA_EXTENDED"}]</div>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaLogoUrl] value="[{$confstrs.sRDFaLogoUrl}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_LOGO_URL"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_LOGO_URL"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaLongitude] value="[{$confstrs.sRDFaLongitude}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_GEO_LONGITUDE"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_GEO_LONGITUDE"}]
                </dd>
                <div class="spacer"></div>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaLatitude] value="[{$confstrs.sRDFaLatitude}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_GEO_LATITUDE"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_GEO_LATITUDE"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaGLN] value="[{$confstrs.sRDFaGLN}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_GLN}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_GLN"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaNAICS] value="[{$confstrs.sRDFaNAICS}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_NAICS}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_NAICS"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaISIC] value="[{$confstrs.sRDFaISIC}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_ISIC}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_ISIC"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[sRDFaDUNS] value="[{$confstrs.sRDFaDUNS}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_DUNS}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_DUNS"}]
                </dd>
                <div class="spacer"></div>
            </dl>


         </div>
    </div>
[{/block}]

[{block name="admin_shop_rdfa_global_offerer_options"}]
    <div class="groupExp">
        <div>
            <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_RDFA_GLOBAL_OFFERING_DATA"}]</b></a>
            <dl>
                <dt>
                    <input type=hidden name="confbools[blShowRDFaProductStock]" value="false">
                    <input type=checkbox name="confbools[blShowRDFaProductStock]" value="true"  [{if ($confbools.blShowRDFaProductStock)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_SHOW_PRODUCTSTOCK}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_SHOW_PRODUCTSTOCK"}]
                </dd>
                <div class="spacer"></div>
            </dl>
            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[iRDFaMinRating] value="[{$confstrs.iRDFaMinRating}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_RATING_MIN"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_RATING_MIN"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <input type=text class="txt" name=confstrs[iRDFaMaxRating] value="[{$confstrs.iRDFaMaxRating}]" [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_RATING_MAX"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_RATING_MAX"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[iRDFaCondition]" class="select" [{$readonly}]>
                        <option value="" [{if ($confstrs.iRDFaCondition == "") || !$confstrs.iRDFaCondition}]selected[{/if}]>-</option>
                        <option value="new" [{if ($confstrs.iRDFaCondition == "new")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COND_NEW"}]</option>
                        <option value="used" [{if ($confstrs.iRDFaCondition == "used")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COND_USED"}]</option>
                        <option value="refurbished" [{if ($confstrs.iRDFaCondition == "refurbished")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COND_REFURBISHED"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_COND"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_COND"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[sRDFaBusinessFnc]" class="select" [{$readonly}]>
                        <option value="" [{if ($confstrs.sRDFaBusinessFnc == "")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_NONE"}]</option>
                        <option value="Sell" [{if ($confstrs.sRDFaBusinessFnc == "Sell")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_SELL"}]</option>
                        <option value="LeaseOut" [{if ($confstrs.sRDFaBusinessFnc == "LeaseOut")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_LEASEOUT"}]</option>
                        <option value="Repair" [{if ($confstrs.sRDFaBusinessFnc == "Repair")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_REPAIR"}]</option>
                        <option value="Maintain" [{if ($confstrs.sRDFaBusinessFnc == "Maintain")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_MAINTAIN"}]</option>
                        <option value="ConstructionInstallation" [{if ($confstrs.sRDFaBusinessFnc == "ConstructionInstallation")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_CONSTINST"}]</option>
                        <option value="ProvideService" [{if ($confstrs.sRDFaBusinessFnc == "ProvideService")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_SERVICE"}]</option>
                        <option value="Dispose" [{if ($confstrs.sRDFaBusinessFnc == "Dispose")}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_FNC_DISPOSE"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_FNC"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_FNC"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select class="select" multiple size="4" name=confarrs[aRDFaCustomers][] [{$readonly}]>
                        <option value="Enduser" [{if $customers.Enduser == 1}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COSTUMER_ENDUSER"}]</option>
                        <option value="Reseller" [{if $customers.Reseller == 1}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COSTUMER_RESELLER"}]</option>
                        <option value="Business" [{if $customers.Business == 1}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COSTUMER_BUSINESS"}]</option>
                        <option value="PublicInstitution" [{if $customers.PublicInstitution == 1}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_COSTUMER_PUBLIC"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_COSTUMER"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_COSTUMER"}]
                </dd>
                <div class="spacer"></div>
            </dl>

            <dl>
                <dt>
                    <select name="confstrs[iRDFaOfferingValidity]" [{$readonly}]>
                        <option value="0" [{if ($confstrs.iRDFaOfferingValidity == 0)}]selected[{/if}]>-</option>
                        <option value="1" [{if ($confstrs.iRDFaOfferingValidity == 1)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_1_DAY"}]</option>
                        <option value="3" [{if ($confstrs.iRDFaOfferingValidity == 3)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_3_DAYS"}]</option>
                        <option value="7" [{if ($confstrs.iRDFaOfferingValidity == 7)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_7_DAYS"}]</option>
                        <option value="14" [{if ($confstrs.iRDFaOfferingValidity == 14)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_14_DAYS"}]</option>
                        <option value="30" [{if ($confstrs.iRDFaOfferingValidity == 30)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_30_DAYS"}]</option>
                        <option value="178" [{if ($confstrs.iRDFaOfferingValidity == 178)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_178_DAYS"}]</option>
                        <option value="356" [{if ($confstrs.iRDFaOfferingValidity == 356)}]selected[{/if}]>[{oxmultilang ident="SHOP_RDFA_356_DAYS"}]</option>
                    </select>
                    [{oxinputhelp ident="HELP_SHOP_RDFA_DURATION_OFFERINGS"}]
                </dt>
                <dd>
                    [{oxmultilang ident="SHOP_RDFA_DURATION_OFFERINGS"}]
                </dd>
                <div class="spacer"></div>
            </dl>

         </div>
    </div>
[{/block}]

<input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>

</form>
<br><br>
[{* @deprecated since v6.0-rc.3 (2017-10-16); GR-Notify registration feature is removed. *}]
[{block name="admin_shop_rdfa_submiturl"}]
[{/block}]


[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]