[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
function loadLang(obj)
{
    var langvar = document.getElementById("catlang");
    if (langvar != null )
        langvar.value = obj.value;
    document.myedit.submit();
}
//-->
</script>

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="category_text">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>


                  [{ $editor }]


        <form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post" onSubmit="copyLongDesc( 'oxcategories__oxlongdesc' );" style="padding: 0px;margin: 0px;height:0px;">
        [{ $oViewConf->getHiddenSid() }]
        <input type="hidden" name="cl" value="category_text">
        <input type="hidden" name="fnc" value="">
        <input type="hidden" name="oxid" value="[{ $oxid }]">
        <input type="hidden" name="voxid" value="[{ $oxid }]">
        <input type="hidden" name="editval[oxcategories__oxid]" value="[{ $oxid }]">
        <input type="hidden" name="catlang" value="[{$catlang}]">
        <input type="hidden" name="editval[oxcategories__oxlongdesc]" value="">
        <table>
        <tr>
          <td valign="top" class="edittext">
          [{if $languages}]<b>[{ oxmultilang ident="GENERAL_LANGUAGE" }]</b>
          <select name="catlang" class="editinput" onchange="Javascript:loadLang(this)" [{ $readonly }]>
          [{foreach key=key item=item from=$languages}]
            <option value="[{$key}]"[{if $catlang == $key}] SELECTED[{/if}]>[{$item->name}]</option>
          [{/foreach}]
          </select>
          [{/if}]
          </td>
        </tr>
        <tr>
          <td>
                <input type="submit" class="edittext" name="save" value="[{ oxmultilang ident="CATEGORY_TEXT_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'">
          </td>
        </tr>
        </form>
      </table>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
