[{block name="admin_formparams"}]
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$cl}]">
    <input type="hidden" name="lstrt" value="[{$lstrt}]">
    <input type="hidden" name="actedit" value="[{$actedit}]">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="fnc" value="[{$fnc}]">
    <input type="hidden" name="language" value="[{$language}]">
    <input type="hidden" name="editlanguage" value="[{$editlanguage}]">
    <input type="hidden" name="delshopid" value="[{$delshopid}]">
    <input type="hidden" name="updatenav" value="[{$updatenav}]">
    [{* sorting *}]
    [{foreach from=$oView->getListSorting() item=aField key=sTable}]
        [{foreach from=$aField item=sSorting key=sField}]
        <input type="hidden" name="sort[[{$sTable}]][[{$sField}]]" value="[{$sSorting}]">
      [{/foreach}]
    [{/foreach}]
[{/block}]