[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="box"}]

<script type="text/javascript">
<!--
function _groupExp(el) {
    var _cur = el.parentNode;

    if (_cur.className == "exp") _cur.className = "";
      else _cur.className = "exp";
}
//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]



<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oTheme->getInfo('id')}]">
    <input type="hidden" name="cl" value="theme_config">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="theme_config">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{$oTheme->getInfo('id')}]">
<input type="hidden" name="editval[oxshops__oxid]" value="[{$oxid}]">

[{cycle assign="_clear_" values=",2"}]

    <b>[{$oTheme->getInfo('title')}]</b><br><br>

    [{foreach from=$var_grouping item=var_list key=var_group}]
        <div class="groupExp">
            <div>
            [{block name="admin_theme_config_form"}]
                <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_THEME_GROUP_`$var_group`"}]</b></a>
                [{foreach from=$var_list item=var_type key=theme_var}]
                    <dl>
                        <dt>
                            [{if $var_type == 'bool'}]
                                <input type=hidden name=confbools[[{$theme_var}]] value=false>
                                <input type=checkbox name=confbools[[{$theme_var}]] value=true  [{if ($confbools.$theme_var)}]checked[{/if}] [{$readonly}]>
                            [{elseif $var_type == 'str'}]
                                <input type=text  class="txt" style="width: 250px;" name=confstrs[[{$theme_var}]] value="[{$confstrs.$theme_var}]" [{$readonly}]>
                            [{elseif $var_type == 'num'}]
                                <input type=text  class="txt" style="width: 50px;" name=confnum[[{$module_var}]] value="[{$confnum.$module_var}]" [{$readonly}]>
                            [{elseif $var_type == 'arr'}]
                                <textarea class="txtfield" name=confarrs[[{$theme_var}]] [{$readonly}]>[{$confarrs.$theme_var}]</textarea>
                            [{elseif $var_type == 'aarr'}]
                                <textarea class="txtfield" style="width: 430px;" name=confaarrs[[{$theme_var}]] wrap="off" [{$readonly}]>[{$confaarrs.$theme_var}]</textarea>
                            [{elseif $var_type == 'select'}]
                                <select class="select" name=confselects[[{$theme_var}]] [{$readonly}]>
                                    [{foreach from=$var_constraints.$theme_var item='_field'}]
                                        <option value="[{$_field|escape}]"  [{if ($confselects.$theme_var==$_field)}]selected[{/if}]>[{oxmultilang ident="SHOP_THEME_`$theme_var`_`$_field`"}]</option>
                                    [{/foreach}]
                                </select>
                            [{/if}]
                            [{oxinputhelp ident="HELP_SHOP_THEME_`$theme_var`"}]
                        </dt>
                        <dd>
                            [{oxmultilang ident="SHOP_THEME_`$theme_var`"}]
                        </dd>
                        <div class="spacer"></div>
                    </dl>
                [{/foreach}]
             [{/block}]
             </div>
         </div>
    [{/foreach}]
<br>
<input type="submit" class="confinput" name="save" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'" [{$readonly}]>
</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]