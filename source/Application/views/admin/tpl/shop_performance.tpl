[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function editThis( sID )
{
    var oTransfer = top.basefrm.edit.document.getElementById("transfer");
    oTransfer.oxid.value = '';
    oTransfer.cl.value = top.oxid.admin.getClass(sID);

    //forcing edit frame to reload after submit
    top.forceReloadingEditFrame();

    var oSearch = top.basefrm.list.document.getElementById("search");
    oSearch.oxid.value = sID;
    oSearch.updatenav.value = 1;
    oSearch.submit();
}
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

[{cycle assign="_clear_" values=",2"}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="shop_performance">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="shop_performance">
    <input type="hidden" name="fnc" value="save">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

    [{include file="include/update_views_notice.tpl"}]
    <table border=0>
        [{block name="admin_shop_performance_form"}]
            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=hidden name=confbools[blCheckTemplates] value=false>
                <input type=checkbox name=confbools[blCheckTemplates] value=true  [{if ($confbools.blCheckTemplates)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_PERF_CHECKIFTPLCOMPILE"}]
             </td>
             <td valign="top" width="100%" >
                [{oxmultilang ident="SHOP_PERF_CHECKIFTPLCOMPILE"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=hidden name=confbools[blLoadVariants] value=false>
                <input type=checkbox name=confbools[blLoadVariants] value=true  [{if ($confbools.blLoadVariants)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_PERF_LOADVARIANTS"}]
             </td>
             <td valign="top" width="100%">
              [{oxmultilang ident="SHOP_PERF_LOADVARIANTS"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=hidden name=confbools[blUseTimeCheck] value=false>
                <input type=checkbox name=confbools[blUseTimeCheck] value=true  [{if ($confbools.blUseTimeCheck)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_PERF_USETIMECHECKINARTLOAD"}]
             </td>
             <td valign="top" width="100%">
              [{oxmultilang ident="SHOP_PERF_USETIMECHECKINARTLOAD"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top" class="nowrap">
               <select class="confinput" name=confstrs[iTop5Mode] [{$readonly}]>
                 <option value="0" [{if $confstrs.iTop5Mode == 0}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_OFF"}]</option>
                 <option value="1" [{if $confstrs.iTop5Mode == 1}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_MANUELL"}]</option>
                 <option value="2" [{if $confstrs.iTop5Mode == 2}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_AUTO"}]</option>
               </select>
               [{oxinputhelp ident="HELP_SHOP_PERF_TOPSELLER"}]
             </td>
             <td valign="top" width="100%">
                  [{oxmultilang ident="SHOP_PERF_TOPSELLER"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
               <select class="confinput" name=confstrs[iNewestArticlesMode] [{$readonly}]>
                 <option value="0" [{if $confstrs.iNewestArticlesMode == 0}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_OFF"}]</option>
                 <option value="1" [{if $confstrs.iNewestArticlesMode == 1}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_MANUELL"}]</option>
                 <option value="2" [{if $confstrs.iNewestArticlesMode == 2}]SELECTED[{/if}]>[{oxmultilang ident="GENERAL_AUTO"}]</option>
               </select>
               [{oxinputhelp ident="HELP_SHOP_PERF_NEWESTARTICLES"}]
             </td>
             <td valign="top" width="100%">
                  [{oxmultilang ident="SHOP_PERF_NEWESTARTICLES"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=hidden name=confbools[blDontShowEmptyCategories] value=false>
                <input type=checkbox class="confinput" name=confbools[blDontShowEmptyCategories] value=true  [{if ($confbools.blDontShowEmptyCategories)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_PERF_DONTSHOWEMTYCATEGORIES"}]
             </td>
             <td valign="top" width="100%" >
               [{oxmultilang ident="SHOP_PERF_DONTSHOWEMTYCATEGORIES"}]
             </td>
            </tr>

            <tr class="conftext[{cycle}]">
             <td valign="top">
                <input type=hidden name=confbools[blClearCacheOnLogout] value=false>
                <input type=checkbox class="confinput" name=confbools[blClearCacheOnLogout] value=true  [{if ($confbools.blClearCacheOnLogout)}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_SHOP_PERF_CLEARCACHEONLOGOUT"}]
             </td>
             <td valign="top" width="100%" >
               [{oxmultilang ident="SHOP_PERF_CLEARCACHEONLOGOUT"}]
             </td>
            </tr>
            <tr class="conftext[{cycle}]">
         		<td valign="top">
            		<input type="hidden" name="confbools[blEnableSeoCache]" value="false">
            		<input type="checkbox" class="confinput" name="confbools[blEnableSeoCache]" value="true"  [{if ($confbools.blEnableSeoCache)}]checked[{/if}] [{$readonly}]>
            		[{oxinputhelp ident="HELP_SHOP_PERF_SEO_CACHE"}]
        		</td>
         	<td valign="top" width="100%" >
           		[{oxmultilang ident="SHOP_PERF_SEO_CACHE"}]
         	</td>
        	</tr>
            <tr class="conftext[{cycle}]">
                <td valign="top">
                    <input type="hidden" name="confbools[blCheckSysReq]" value="false">
                    <input type="checkbox" class="confinput" name="confbools[blCheckSysReq]" value="true"  [{if ($confbools.blCheckSysReq)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_SYSREQ_CHECK"}]
                </td>
                <td valign="top" width="100%" >
                    [{oxmultilang ident="SHOP_PERF_SYSREQ_CHECK"}]
                </td>
            </tr>
        [{/block}]
    </table>
    <br>

    <fieldset title="[{oxmultilang ident="SHOP_PERF_PERFORMANCE"}]">
    <legend>[{oxmultilang ident="SHOP_PERF_EXTERNALPERFORMANCE"}]</legend><br>
    <table border=0>
    [{block name="admin_shop_performance_enhancedform"}]
        <tr>
            <td width="50%" valign="top">
                <table border=0 width="100%">

                <tr>
                 <td valign="top" class="conftext nowrap">
                    <input type=hidden name=confbools[bl_perfLoadAktion] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadAktion] value=true  [{if ($confbools.bl_perfLoadAktion)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADACTION"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADACTION"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadCrossselling] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadCrossselling] value=true  [{if ($confbools.bl_perfLoadCrossselling)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADCROSSSELLING"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADCROSSSELLING"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadAccessoires] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadAccessoires] value=true  [{if ($confbools.bl_perfLoadAccessoires)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADACCESSOIRES"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADACCESSOIRES"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadCustomerWhoBoughtThis] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadCustomerWhoBoughtThis] value=true  [{if ($confbools.bl_perfLoadCustomerWhoBoughtThis)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADCUSTOMERWHOBOUGHTTHIS"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADCUSTOMERWHOBOUGHTTHIS"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadSimilar] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadSimilar] value=true  [{if ($confbools.bl_perfLoadSimilar)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADSIMILAR"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                 [{oxmultilang ident="SHOP_PERF_LOADSIMILAR"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadSelectLists] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadSelectLists] value=true  [{if ($confbools.bl_perfLoadSelectLists)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADSELECTLIST"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADSELECTLIST"}]
                 </td>
                </tr>

                <tr>
                  <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadSelectListsInAList] value=false>
                    <input type=checkbox class="confinput" name=confbools[bl_perfLoadSelectListsInAList] value=true  [{if ($confbools.bl_perfLoadSelectListsInAList)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADSELECTLISTSINALIST"}]
                  </td>
                  <td valign="top" class="conftext" width="100%" >
                    [{oxmultilang ident="SHOP_PERF_LOADSELECTLISTSINALIST"}]
                  </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadManufacturerTree] value=false>
                    <input type=checkbox class="confinput" name=confbools[bl_perfLoadManufacturerTree] value=true  [{if ($confbools.bl_perfLoadManufacturerTree)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADMANUFACTURERTREE"}]
                 </td>
                 <td valign="top" class="conftext" width="100%" >
                   [{oxmultilang ident="SHOP_PERF_LOADMANUFACTURERTREE"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfUseSelectlistPrice] value=false>
                    <input type=checkbox class="confinput" name=confbools[bl_perfUseSelectlistPrice] value=true  [{if ($confbools.bl_perfUseSelectlistPrice)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_USESELECTLISTPRICE"}]
                 </td>
                 <td valign="top" class="conftext" width="100%" >
                   [{oxmultilang ident="SHOP_PERF_USESELECTLISTPRICE"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[blPerfNoBasketSaving] value=false>
                    <input type=checkbox class="confinput" name=confbools[blPerfNoBasketSaving] value=true  [{if ($confbools.blPerfNoBasketSaving)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_DISBASKETSAVING"}]
                 </td>
                 <td valign="top" class="conftext" width="100%" >
                   [{oxmultilang ident="SHOP_PERF_DISBASKETSAVING"}]
                 </td>
                </tr>

                </table>
            </td>

            <td valign="top">

                <table border=0 width="100%">

                <tr>
                 <td valign="top" class="conftext nowrap">
                    <input type=hidden name=confbools[bl_perfLoadDelivery] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadDelivery] value=true  [{if ($confbools.bl_perfLoadDelivery)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADDELIVERY"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADDELIVERY"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadPrice] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadPrice] value=true  [{if ($confbools.bl_perfLoadPrice)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADPRICE"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADPRICE"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadPriceForAddList] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadPriceForAddList] value=true  [{if ($confbools.bl_perfLoadPriceForAddList)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADPRICEFORADDLIST"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADPRICEFORADDLIST"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfShowActionCatArticleCnt] value=false>
                    <input type=checkbox name=confbools[bl_perfShowActionCatArticleCnt] value=true  [{if ($confbools.bl_perfShowActionCatArticleCnt)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_SHOWACTCATARTCOUNT"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_SHOWACTCATARTCOUNT"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadCurrency] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadCurrency] value=true  [{if ($confbools.bl_perfLoadCurrency)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADCURRENCY"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADCURRENCY"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadLanguages] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadLanguages] value=true  [{if ($confbools.bl_perfLoadLanguages)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADLANGUAGES"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADLANGUAGES"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadNews] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadNews] value=true  [{if ($confbools.bl_perfLoadNews)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADNEWS"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADNEWS"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfLoadNewsOnlyStart] value=false>
                    <input type=checkbox name=confbools[bl_perfLoadNewsOnlyStart] value=true  [{if ($confbools.bl_perfLoadNewsOnlyStart)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_LOADNEWSONLYSTART"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_LOADNEWSONLYSTART"}]
                 </td>
                </tr>

                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfParseLongDescinSmarty] value=false>
                    <input type=checkbox name=confbools[bl_perfParseLongDescinSmarty] value=true  [{if ($confbools.bl_perfParseLongDescinSmarty)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_PARSELONGDESCINSMARTY"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_PARSELONGDESCINSMARTY"}]
                 </td>
                </tr>
                <tr>
                 <td valign="top" class="conftext">
                    <input type=hidden name=confbools[bl_perfCalcVatOnlyForBasketOrder] value=false>
                    <input type=checkbox name=confbools[bl_perfCalcVatOnlyForBasketOrder] value=true  [{if ($confbools.bl_perfCalcVatOnlyForBasketOrder)}]checked[{/if}] [{$readonly}]>
                    [{oxinputhelp ident="HELP_SHOP_PERF_CALCVATONLYFORBASKETORDER"}]
                 </td>
                 <td valign="top" class="conftext" width="100%">
                  [{oxmultilang ident="SHOP_PERF_CALCVATONLYFORBASKETORDER"}]
                 </td>
                </tr>
                </table>
            </td>
        </tr>
    [{/block}]
    </table>
    </fieldset>
        <br>
         <input type="submit" class="confinput" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
