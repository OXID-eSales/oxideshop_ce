[{if $listitem->oxcategories__oxleft->value + 1 == $listitem->oxcategories__oxright->value}]
    <a href="Javascript:top.oxid.admin.deleteThis('[{$listitem->oxcategories__oxid->value}]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
[{/if}]