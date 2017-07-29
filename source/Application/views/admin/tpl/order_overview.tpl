[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="order_overview">
</form>

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
    <tr>
        <td valign="top" class="edittext" width="50%">
        [{if $edit}]
            <table width="200" border="0" cellspacing="0" cellpadding="0" nowrap>
            <tr><td class="edittext" valign="top">
            [{block name="admin_order_overview_billingaddress"}]
                <b>[{oxmultilang ident="GENERAL_BILLADDRESS"}]</b><br>
                <br>
                [{if $edit->oxorder__oxbillcompany->value}][{oxmultilang ident="GENERAL_COMPANY"}] [{$edit->oxorder__oxbillcompany->value}]<br>[{/if}]
                [{if $edit->oxorder__oxbilladdinfo->value}][{$edit->oxorder__oxbilladdinfo->value}]<br>[{/if}]
                <a class="jumplink" href="[{$oViewConf->getSelfLink()}]cl=admin_user&oxid=[{$edit->oxorder__oxuserid->value}]" target="basefrm" onclick="_homeExpActByName('admin_user');">[{$edit->oxorder__oxbillsal->value|oxmultilangsal}] [{$edit->oxorder__oxbillfname->value}] [{$edit->oxorder__oxbilllname->value}]</a><br>
                [{$edit->oxorder__oxbillstreet->value}] [{$edit->oxorder__oxbillstreetnr->value}]<br>
                [{$edit->oxorder__oxbillstateid->value}]
                [{$edit->oxorder__oxbillzip->value}] [{$edit->oxorder__oxbillcity->value}]<br>
                [{$edit->oxorder__oxbillcountry->value}]<br>
                [{if $edit->oxorder__oxbillcompany->value && $edit->oxorder__oxbillustid->value}]
                    <br>
                    [{oxmultilang ident="ORDER_OVERVIEW_VATID"}]:
                    [{$edit->oxorder__oxbillustid->value}]<br>
                    [{include file="include/message_vat_check_failed.tpl"}]
                [{/if}]
                <br>
                [{oxmultilang ident="GENERAL_EMAIL"}]: <a href="mailto:[{$edit->oxorder__oxbillemail->value}]?subject=[{$actshop}] - [{oxmultilang ident="GENERAL_ORDERNUM"}] [{$edit->oxorder__oxordernr->value}]" class="edittext"><em>[{$edit->oxorder__oxbillemail->value}]</em></a><br>
                <br>
            [{/block}]
            </td>
            [{if $edit->oxorder__oxdelstreet->value}]
            <td class="edittext" valign="top">
                [{block name="admin_order_overview_deliveryaddress"}]
                    <b>[{oxmultilang ident="GENERAL_DELIVERYADDRESS"}]:</b><br>
                    <br>
                    [{if $edit->oxorder__oxdelcompany->value}][{oxmultilang ident="GENERAL_COMPANY"}] [{$edit->oxorder__oxdelcompany->value}]<br>[{/if}]
                    [{if $edit->oxorder__oxdeladdinfo->value}][{$edit->oxorder__oxdeladdinfo->value}]<br>[{/if}]
                    [{$edit->oxorder__oxdelsal->value|oxmultilangsal}] [{$edit->oxorder__oxdelfname->value}] [{$edit->oxorder__oxdellname->value}]<br>
                    [{$edit->oxorder__oxdelstreet->value}] [{$edit->oxorder__oxdelstreetnr->value}]<br>
                    [{$edit->oxorder__oxdelstateid->value}]
                    [{$edit->oxorder__oxdelzip->value}] [{$edit->oxorder__oxdelcity->value}]<br>
                    [{$edit->oxorder__oxdelcountry->value}]<br>
                    <br>
                [{/block}]
            </td>
            [{/if}]
            </tr></table>
            <b>[{oxmultilang ident="GENERAL_ITEM"}]:</b><br>
            <br>
            <table cellspacing="0" cellpadding="0" border="0">
            [{block name="admin_order_overview_items"}]
                [{foreach from=$orderArticles item=listitem}]
                <tr>
                    <td valign="top" class="edittext">[{$listitem->oxorderarticles__oxamount->value}] * </td>
                    <td valign="top" class="edittext">&nbsp;[{$listitem->oxorderarticles__oxartnum->value}]</td>
                    <td valign="top" class="edittext">&nbsp;[{$listitem->oxorderarticles__oxtitle->getRawValue()|oxtruncate:20:""|strip_tags}][{if $listitem->oxwrapping__oxname->value}]&nbsp;([{$listitem->oxwrapping__oxname->value}])&nbsp;[{/if}]</td>
                    <td valign="top" class="edittext">&nbsp;[{$listitem->oxorderarticles__oxselvariant->value}]</td>
                    [{if $edit->isNettoMode()}]
                        <td valign="top" class="edittext">&nbsp;&nbsp;[{$listitem->getNetPriceFormated()}] [{$edit->oxorder__oxcurrency->value}]</td>
                    [{else}]
                        <td valign="top" class="edittext">&nbsp;&nbsp;[{$listitem->getTotalBrutPriceFormated()}] [{$edit->oxorder__oxcurrency->value}]</td>
                    [{/if}]
                    [{if $listitem->getPersParams()}]
                    <td valign="top" class="edittext">
                        [{block name="admin_order_overview_persparams"}]
                            [{include file="include/persparams.tpl" persParams=$listitem->getPersParams()}]
                        [{/block}]
                    </td>
                    [{/if}]
                </tr>
                [{/foreach}]
            [{/block}]
            </table>
            <br>
            [{if $edit->oxorder__oxstorno->value}]
            <span class="orderstorno">[{oxmultilang ident="ORDER_OVERVIEW_STORNO"}]</span><br><br>
            [{/if}]
            <b>[{oxmultilang ident="GENERAL_ATALL"}]: </b><br><br>
            [{block name="admin_order_overview_total"}]
                [{include file="include/order_info.tpl"}]
            [{/block}]

            <br>
            <table>
            [{block name="admin_order_overview_checkout"}]
                <tr>
                    <td class="edittext">[{oxmultilang ident="ORDER_OVERVIEW_PAYMENTTYPE"}]: </td>
                    <td class="edittext"><b>[{$paymentType->oxpayments__oxdesc->value}]</b></td>
                </tr>
                <tr>
                    <td class="edittext">[{oxmultilang ident="ORDER_OVERVIEW_DELTYPE"}]: </td>
                    <td class="edittext"><b>[{$deliveryType->oxdeliveryset__oxtitle->value}]</b><br></td>
                </tr>
            [{/block}]
            </table>

            <br>
            [{if $paymentType->aDynValues}]
                <table cellspacing="0" cellpadding="0" border="0">
                [{block name="admin_order_overview_dynamic"}]
                    [{foreach from=$paymentType->aDynValues item=value}]
                    [{assign var="ident" value='ORDER_OVERVIEW_'|cat:$value->name}]
                    [{assign var="ident" value=$ident|oxupper}]
                    <tr>
                        <td class="edittext">
                        [{oxmultilang ident=$ident}]:&nbsp;
                        </td>
                        <td class="edittext">
                           [{$value->value}]
                        </td>
                    </tr>
                    [{/foreach}]
                [{/block}]
                </table><br>
            [{/if}]
            [{if $edit->oxorder__oxremark->value}]
            <b>[{oxmultilang ident="GENERAL_REMARK"}]</b>
            <table cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td class="edittext wrap">[{$edit->oxorder__oxremark->value}]</td>
                </tr>
            </table>
            [{/if}]
        [{/if}]
        </td>
        <td>&nbsp;&nbsp;
        </td>
        <td valign="top" class="edittext">
            [{if $edit}]
            [{block name="admin_order_overview_order_number"}]
                <b>[{oxmultilang ident="GENERAL_ORDERNUM"}]: </b>[{$edit->oxorder__oxordernr->value}]<br>
            [{/block}]
            [{block name="admin_order_overview_customer_number"}]
                [{assign var="user" value=$edit->getOrderUser()}]
                <b>[{oxmultilang ident="CUSTOMERNUM"}]: </b><a class="jumplink" href="[{$oViewConf->getSelfLink()}]cl=admin_user&oxid=[{$edit->oxorder__oxuserid->value}]" target="basefrm" onclick="_homeExpActByName('admin_user');">[{$user->oxuser__oxcustnr->value}]</a><br>
            [{/block}]
            <br>
                <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="order_overview">
                <input type="hidden" name="fnc" value="changefolder">
                <input type="hidden" name="oxid" value="[{$oxid}]">
                <input type="hidden" name="folderclass" value="oxorder">
                [{block name="admin_order_overview_folder_form"}]
                    [{oxmultilang ident="ORDER_OVERVIEW_INFOLDER"}]:&nbsp;
                    <select name="setfolder" class="folderselect" onChange="document.myedit.submit();" [{$readonly}]>
                    [{foreach from=$afolder key=field item=color}]
                    <option value="[{$field}]" [{if $edit->oxorder__oxfolder->value == $field || ($field|oxmultilangassign == $edit->oxorder__oxfolder->value)}]SELECTED[{/if}] style="color: [{$color}];">[{oxmultilang ident=$field noerror=true}]</option>
                    [{/foreach}]
                    </select>
                    [{oxinputhelp ident="HELP_ORDER_OVERVIEW_INFOLDER"}]
                    &nbsp;&nbsp;
                [{/block}]
                </form>
            [{/if}]
            [{if $edit && $edit->oxorder__oxtransstatus->value}]
                [{block name="admin_order_overview_status"}]
                    [{oxmultilang ident="ORDER_OVERVIEW_INTSTATUS"}]:&nbsp;<b>[{$edit->oxorder__oxtransstatus->value}]</b><br>
                [{/block}]
            [{/if}]
            <br>
            <b>[{oxmultilang ident="GENERAL_REVIEW"}]: </b>
            <br>
            <table cellspacing="0" cellpadding="0" border="0">
            [{block name="admin_order_overview_general"}]
                <tr>
                    <td class="edittext" height="20">
                    [{oxmultilang ident="ORDER_OVERVIEW_ORDERAMTODAY"}]:
                    </td>
                    <td class="edittext">
                    &nbsp;<b>[{$ordercnt}]</b>
                    </td>
                </tr>
                <tr>
                    <td class="edittext" height="20">
                    [{oxmultilang ident="ORDER_OVERVIEW_ORDERSUMTODAY"}]:
                    </td>
                    <td class="edittext">
                    &nbsp;<b>[{$ordersum}]</b> [{$currency->name}]
                    </td>
                </tr>
                <tr>
                    <td class="edittext" height="20">
                    [{oxmultilang ident="ORDER_OVERVIEW_ORDERAMTOTAL"}]:
                    </td>
                    <td class="edittext">
                    &nbsp;<b>[{$ordertotalcnt}]</b>
                    </td>
                </tr>
                <tr>
                    <td class="edittext" height="20">
                    [{oxmultilang ident="ORDER_OVERVIEW_ORDERSUMTOTAL"}]:
                    </td>
                    <td class="edittext">
                    &nbsp;<b>[{$ordertotalsum}]</b> [{$currency->name}]
                    </td>
                </tr>
            [{/block}]
            </table>
        <br>
        [{if $edit}]
        <table cellspacing="0" cellpadding="0" border="0">
        <form name="sendorder" id="sendorder" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="order_overview">
        <input type="hidden" name="fnc" value="sendorder">
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="editval[oxorder__oxid]" value="[{$oxid}]">
        [{block name="admin_order_overview_send_form"}]
            <tr>
                <td class="edittext">
                </td>
                <td class="edittext" style="border : 1px #A9A9A9; border-style : solid solid solid solid; padding-top: 5px; padding-bottom: 5px; padding-right: 5px; padding-left: 5px;">
                    <input type="submit" class="edittext" name="save" value="&nbsp;&nbsp;[{oxmultilang ident="GENERAL_NOWSEND"}]&nbsp;&nbsp;" [{$readonly}]><br>
                    [{oxmultilang ident="GENERAL_SENDEMAIL"}] <input class="edittext" type="checkbox" name="sendmail" value='1' [{$readonly}]>
                </td>
            </tr>
            </form>
            <tr>
                <td class="edittext">
                </td>
                <td class="edittext" valign="bottom"><br>
                [{if $oView->canResetShippingDate()}]
                    <b>[{oxmultilang ident="GENERAL_SENDON"}]</b><b>[{$edit->oxorder__oxsenddate|oxformdate:'datetime':true}]</b>
                [{else}]
                    <b>[{oxmultilang ident="GENERAL_NOSENT"}]</b>
                [{/if}]
                </td>
            </tr>
        [{/block}]
        [{if $oView->canResetShippingDate()}]
        <form name="resetorder" id="resetorder" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="order_overview">
        <input type="hidden" name="fnc" value="resetorder">
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="editval[oxorder__oxid]" value="[{$oxid}]">
        [{block name="admin_order_overview_reset_form"}]
            <tr>
                <td class="edittext">
                </td>
                <td class="edittext"><br>
                    <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SETBACKSENDTIME"}]" [{$readonly}]>
                </td>
            </tr>
        [{/block}]
        </form>
        [{/if}]
        </table>
        [{/if}]
        </td>

        <td valign="top" class="edittext" align="right">
            [{block name="admin_order_overview_export"}]
            [{/block}]
        </td>
    </tr>
    </table>
[{include file="bottomnaviitem.tpl"}]
</table>
[{include file="bottomitem.tpl"}]
