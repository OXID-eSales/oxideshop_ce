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

[{ if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]



<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{$oModule->getInfo('id')}]">
    <input type="hidden" name="cl" value="module_config">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="actshop" value="[{$oViewConf->getActiveShopId()}]">
    <input type="hidden" name="updatenav" value="">
    <input type="hidden" name="editlanguage" value="[{ $editlanguage }]">
</form>

<form name="myedit" id="myedit" action="[{ $oViewConf->getSelfLink() }]" method="post">
[{ $oViewConf->getHiddenSid() }]
<input type="hidden" name="cl" value="module_config">
<input type="hidden" name="fnc" value="save">
<input type="hidden" name="oxid" value="[{$oModule->getInfo('id')}]">
<input type="hidden" name="editval[oxshops__oxid]" value="[{ $oxid }]">

[{cycle assign="_clear_" values=",2" }]

    [{foreach from=$var_grouping item=var_list key=var_group}]
        <div class="groupExp">
            <div>
                <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{ oxmultilang ident="SHOP_MODULE_GROUP_`$var_group`" }]</b></a>
                [{foreach from=$var_list item=var_type key=module_var}]
                    <dl>
                        <dt>
                            [{if $var_type == 'bool'}]
                                <input type=hidden name=confbools[[{$module_var}]] value=false>
                                <input type=checkbox name=confbools[[{$module_var}]] value=true  [{if ($confbools.$module_var)}]checked[{/if}] [{ $readonly }]>
                            [{elseif $var_type == 'str'}]
                                <input type=text  class="txt" style="width: 250px;" name=confstrs[[{$module_var}]] value="[{$confstrs.$module_var}]" [{ $readonly }]>
                            [{elseif $var_type == 'num'}]
                                <input type=text  class="txt" style="width: 50px;" name=confnum[[{$module_var}]] value="[{$confnum.$module_var}]" [{ $readonly }]>
                            [{elseif $var_type == 'arr'}]
                                <textarea class="txtfield" name=confarrs[[{$module_var}]] [{ $readonly }]>[{$confarrs.$module_var}]</textarea>
                            [{elseif $var_type == 'aarr'}]
                                <textarea class="txtfield" style="width: 430px;" name=confaarrs[[{$module_var}]] wrap="off" [{ $readonly }]>[{$confaarrs.$module_var}]</textarea>
                            [{elseif $var_type == 'select'}]
                                <select class="select" name=confselects[[{$module_var}]] [{ $readonly }]>
                                    [{foreach from=$var_constraints.$module_var item='_field'}]
                                        <option value="[{$_field|escape}]"  [{if ($confselects.$module_var==$_field)}]selected[{/if}]>[{ oxmultilang ident="SHOP_MODULE_`$module_var`_`$_field`" }]</option>
                                    [{/foreach}]
                                </select>
                            [{/if}]
                            [{oxinputhelp ident="HELP_SHOP_MODULE_`$module_var`"}]
                        </dt>
                        <dd>
                            [{oxmultilang ident="SHOP_MODULE_`$module_var`"}]
                        </dd>
                        <div class="spacer"></div>
                    </dl>
                [{/foreach}]
             </div>
         </div>
    [{/foreach}]
<br>
[{if $var_grouping}]
    <input type="submit" class="confinput" name="save" value="[{ oxmultilang ident="GENERAL_SAVE" }]" onClick="Javascript:document.myedit.fnc.value='save'" [{ $readonly }]>
[{/if}]
</form>
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]