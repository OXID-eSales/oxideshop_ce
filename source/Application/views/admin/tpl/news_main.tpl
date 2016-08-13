[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="news_main">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>


<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="news_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="voxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxnews__oxid]" value="[{$oxid}]">

<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_news_main_form"}]
            <tr>
                <td class="edittext" width="90">
                [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[oxnews__oxactive]" value='1' [{if $edit->oxnews__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_ACTIVFROMTILL"}]
                </td>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_FROM"}]<input type="text" class="editinput" size="30" name="editval[oxnews__oxactivefrom]" value="[{$edit->oxnews__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]><br>
                [{oxmultilang ident="GENERAL_TILL"}] <input type="text" class="editinput" size="30" name="editval[oxnews__oxactiveto]" value="[{$edit->oxnews__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_ACTIVFROMTILL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_DATE"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="15" maxlength="[{$edit->oxnews__oxdate->fldmax_length}]" name="editval[oxnews__oxdate]" value="[{$edit->oxnews__oxdate|oxformdate}]" [{include file="help.tpl" helpid=article_delivery}] [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_DATE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="NEWS_MAIN_SHORTDESC"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="[{$edit->oxnews__oxshortdesc->fldmax_length}]" name="editval[oxnews__oxshortdesc]" value="[{$edit->oxnews__oxshortdesc->value}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_NEWS_MAIN_SHORTDESC"}]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                 [{include file="language_edit.tpl"}]
            </td>
        </tr>

        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
            <input type="submit" class="edittext" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
            </td>
        </tr>
        </table>
    </td>
    <!-- Anfang rechte Seite -->
    <td valign="top" class="edittext vr" align="left" width="50%">
        [{block name="admin_news_main_assign_groups"}]
            [{if $oxid != "-1"}]
                <input [{$readonly}] type="button" value="[{oxmultilang ident="GENERAL_ASSIGNGROUPS"}]" class="edittext" onclick="JavaScript:showDialog('&cl=news_main&aoc=1&oxid=[{$oxid}]');">
            [{/if}]
        [{/block}]
    </td>
    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
