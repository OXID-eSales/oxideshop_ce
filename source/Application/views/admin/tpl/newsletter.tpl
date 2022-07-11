[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
    [{else}]
    [{assign var="readonly" value=""}]
    [{/if}]

<form name="export_recipients" id="export_recipients" action="[{$oViewConf->getSelfLink()}]" method="get"
      onSubmit="copyLongDesc( 'oxnewsletter__oxtemplate' );">
    [{$oViewConf->getHiddenSid() nofilter}]
    <input type="hidden" name="cl" value="admin_newsletter">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="editval[oxnewsletter__oxtemplate]" value="">

    <table cellspacing="0" cellpadding="0" border="0" width="98%;">
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
                <input type="submit" class="edittext" name="export" id="newsletter_recipients"
                       value="[{oxmultilang ident="tbclnewsletter_recipients"}]"
                       onClick="Javascript:document.export_recipients.fnc.value='export'"" [{$readonly}]>
            </td>
        </tr>
    </table>

</form>
[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
