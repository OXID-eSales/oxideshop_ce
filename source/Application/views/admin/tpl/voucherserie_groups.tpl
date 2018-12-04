[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="voucherserie_groups">
</form>

[{block name="admin_voucherserie_relations"}]
    [{if $oxid != "-1"}]
        <table width="100%">
            <colgroup span="3" width="33%">
            <tr>
                <td>
                    [{block name="admin_voucherserie_groups_form"}]
                        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="voucherserie_groups">
                            <input type="hidden" name="fnc" value="">
                            <input type="hidden" name="oxid" value="[{$oxid}]">
                            <input type="hidden" name="editval[oxuser__oxid]" value="[{$oxid}]">
                            <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNGROUPS"}]" class="edittext" onclick="JavaScript:showDialog('&cl=voucherserie_groups&aoc=1&oxid=[{$oxid}]');">
                        </form>
                    [{/block}]
                </td>
                <td>
                    [{block name="admin_voucherserie_categories_form"}]
                        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="discount_articles">
                            <input type="hidden" name="fnc" value="">
                            <input type="hidden" name="oxid" value="[{$oxid}]">
                            <input type="hidden" name="editval[discount__oxid]" value="[{$oxid}]">
                            <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCATEGORIES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=discount_articles&aoc=2&oxid=[{$oxid}]');">
                        </form>
                    [{/block}]
                </td>
                <td>
                    [{block name="admin_voucherserie_articles_form"}]
                        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
                            [{$oViewConf->getHiddenSid()}]
                            <input type="hidden" name="cl" value="discount_articles">
                            <input type="hidden" name="fnc" value="">
                            <input type="hidden" name="oxid" value="[{$oxid}]">
                            <input type="hidden" name="editval[discount__oxid]" value="[{$oxid}]">
                            <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNARTICLES"}]" class="edittext" onclick="JavaScript:showDialog('&cl=discount_articles&aoc=1&oxid=[{$oxid}]');">
                        </form>
                    [{/block}]
                </td>
            </tr>
        </table>
    [{/if}]
[{/block}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
