[{block name="persparams_checkout_basketcontents_editable"}]
    <label class="persParamLabel">
        [{if $label}][{oxmultilang ident=$label}][{else}][{$key}][{/if}]:
    </label>
    <input class="textbox persParam" type="text" name="[{$inputname}][[{$key}]]" value="[{$value}]">
[{/block}]