[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly || $edit->blForeignArticle }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="article_overview">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>


    <table cellspacing="0" cellpadding="0" border="0" width="98%">
    <tr>
        <td valign="top" class="edittext">
            <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="edittext" width="150">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_ARTMADEON" }]
                </td>
                <td class="edittext">
                [{$edit->oxarticles__oxinsert|oxformdate}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_LASTCHANGE" }]
                </td>
                <td class="edittext">
                [{$edit->oxarticles__oxtimestamp|oxformdate:"datetime"}]
                </td>
            </tr>
            <tr>
                <td class="edittext" height="20">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_SALEPOSITION" }]
                </td>
                <td class="edittext">
                :&nbsp;<b>[{ $postopten }]/[{$toptentotal}]</b>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_TOTALORDERCNT" }]
                </td>
                <td class="edittext">
                :&nbsp;<b>[{ $totalordercnt }]</b>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_SOLDCNT" }]
                </td>
                <td class="edittext">
                :&nbsp;<b>[{ $soldcnt }]</b>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_CANCELEDCNT" }]
                </td>
                <td class="edittext">
                :&nbsp;<b>[{ $canceledcnt }]</b>
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{ oxmultilang ident="ARTICLE_OVERVIEW_LEFTORDERCNT" }]
                </td>
                <td class="edittext">
                :&nbsp;<b>[{ $leftordercnt }]</b>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    </table>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
