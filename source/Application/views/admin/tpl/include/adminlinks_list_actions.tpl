[{if !$readonly}]
    <a href="Javascript:top.oxid.admin.deleteThis('[{$listitem->oxlinks__oxid->value}]');" class="delete" id="del.[{$_cnt}]" [{include file="help.tpl" helpid=item_delete}]></a>
[{/if}]