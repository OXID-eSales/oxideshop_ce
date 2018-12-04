[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="newsletter_selection">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="newsletter_selection">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxnewsletter__oxid]" value="[{$oxid}]">
    <table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
        <tr>
            <td valign="top" class="edittext">
                <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNGROUPS"}]" class="edittext" onclick="JavaScript:showDialog('&cl=newsletter_selection&aoc=1&oxid=[{$oxid}]');">
            </td>

            <!-- Anfang rechte Seite -->
            <td valign="top" class="edittext" align="left">
                <br>
                <b>[{oxmultilang ident="NEWSLETTER_SELECTION_SELMAILRESAVER"}]:</b> <b id="_newsletterusercnt">[{$oView->getUserCount()}]</b>
                <br><br>
                <div id="_newsletterbtn"[{if !$oView->getUserCount()}]style="display:none"[{/if}]>
                    <a href="[{$oViewConf->getSelfLink()}]&amp;cl=newsletter_send&amp;oxid=[{$oxid}]&amp;iStart=0" class="edittext" target="list" [{if $readonly}]onclick="JavaScript:return false;"[{/if}]><b>[{oxmultilang ident="NEWSLETTER_SELECTION_SENDNEWS"}]</b></a>
                </div>
            </td>
        </tr>
    </table>
</form>

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
