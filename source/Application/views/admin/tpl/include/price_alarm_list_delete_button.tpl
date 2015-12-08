[{if !$listitem->isOx() && $readonly == ""}]
    <a href="Javascript:top.oxid.admin.deleteThis('[{$listitem->oxpricealarm__oxid->value}]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
[{/if}]