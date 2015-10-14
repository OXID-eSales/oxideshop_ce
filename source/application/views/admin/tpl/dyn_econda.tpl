[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="1">
    <input type="hidden" name="cl" value="">
</form>


        <table cellspacing="0" cellpadding="0" border="0">
        <form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
        [{$oViewConf->getHiddenSid()}]
        <input type="hidden" name="cl" value="dyn_econda">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{$oxid}]">
        <input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">
           <tr>
              <td valign="top" class="edittext">
                   [{oxmultilang ident="DYN_ECONDA_ACTIVE"}]&nbsp;&nbsp;
              </td>
              <td valign="top" class="edittext">
                <input type=hidden name=confbools[blEcondaActive] value=false>
                <input type=checkbox name=confbools[blEcondaActive] value=true  [{if ($confbools.blEcondaActive)}]checked[{/if}] [{$readonly}]>
              </td>
           </tr>
        <tr>
            <td class="edittext">
            </td>
            <td class="edittext"><br>
               <input type="submit" class="confinput" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]>
            </td>
        </tr>
            </form>
        </table>
        <br><br>
         [{assign var='oxGetEcondaModule' value=$oViewConf->getBaseDir()}]
         [{assign var='oxGetEcondaModule' value="`$oxGetEcondaModule`modules/econda/out/"}]
         [{oxmultilang ident="DYN_ECONDA_ATTENTION"}]<br>
         [{oxmultilang ident="DYN_ECONDA_COPY_FILE" args=$oxGetEcondaModule}]


[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
