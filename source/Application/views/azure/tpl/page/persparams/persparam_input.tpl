[{block name="persparams_persparam_input"}]
    <label class="persParamLabel">
        [{if $label}][{oxmultilang ident=$label}][{else}][{$key}][{/if}]:
    </label>
    <input class="textbox persParam" type="text" name="[{$inputname}][[{$key}]]" value="[{$value}]">
[{/block}]